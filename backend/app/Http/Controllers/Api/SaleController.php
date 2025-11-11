<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockTransaction;
use App\Models\CustomerTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'status', 'paymentMethod', 'details.product', 'createdBy', 'updatedBy']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        return SaleResource::collection($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_id' => 'required|exists:payment_methods,id',
            'paid_amount' => 'nullable|numeric|min:0',
            'status_id' => 'required|exists:statuses,id',
            'remark' => 'nullable|string|max:1000',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
            'sale_date' => 'nullable|date',
            'warehouse_id' => 'required|exists:inventories,warehouse_id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Calculate total amount
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                $totalAmount += $product->price * $item['quantity'];
            }

            // 2. Calculate change (due_amount)
            $paidAmount = $request->paid_amount;
            $dueAmount = $paidAmount - $totalAmount; // change amount

            if ($dueAmount < 0) {
                $dueAmount = 0; // avoid negative change
            }

            // 3. Create Sale
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_id' => $request->payment_id,
                'status_id' => $request->status_id,
                'remark' => $request->remark ?? null,
                'sale_date' => $request->sale_date ?? now(),
                'created_by' => $request->created_by,
                'updated_by' => $request->updated_by ?? $request->created_by,
            ]);

            // 4. Create Sale Details and Stock Transactions
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $product->price * $item['quantity'],
                ]);

                // Safe Inventory lookup or create if missing
                $inventory = Inventory::firstOrCreate(
                    ['product_id' => $product->id, 'warehouse_id' => $request->warehouse_id],
                    [
                        'qty' => 0,
                        'name' => $product->name,
                        'created_by' => $request->created_by,
                        'updated_by' => $request->updated_by ?? $request->created_by, 
                    ]
                );

                $inventory->decrement('qty', $item['quantity']);

                StockTransaction::create([
                    'inventory_id' => $inventory->id,
                    'reference_id' => $sale->id,
                    'reference_type' => 'sale',
                    'quantity_change' => -$item['quantity'],
                    'type' => 'out',
                    'created_by' => $request->created_by,
                    'updated_by' => $request->updated_by ?? $request->created_by,
                ]);
            }

            DB::commit();

            return new SaleResource($sale->fresh(['customer', 'status', 'paymentMethod', 'details.product', 'createdBy', 'updatedBy']));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create sale', 'details' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $sale = Sale::with(['customer', 'status', 'paymentMethod', 'details.product', 'createdBy', 'updatedBy'])->findOrFail($id);
        return new SaleResource($sale);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'payment_id' => 'sometimes|required|exists:payment_methods,id',
            'paid_amount' => 'sometimes|required|numeric|min:0',
            'status_id' => 'sometimes|required|exists:statuses,id',
            'remark' => 'nullable|string|max:1000',
            'sale_date' => 'sometimes|date',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        $sale = Sale::with('status')->findOrFail($id); // Load sale with status relation

        // 1. Store old status name for comparison
        $oldStatus = $sale->status->name ?? null;

        DB::beginTransaction();
        try {
            // 2️. Update sale fields
            $sale->update([
                'payment_id' => $request->payment_id ?? $sale->payment_id,
                'paid_amount' => $request->paid_amount ?? $sale->paid_amount,
                'status_id' => $request->status_id ?? $sale->status_id,
                'remark' => $request->remark ?? $sale->remark,
                'sale_date' => $request->sale_date ?? $sale->sale_date,
                'updated_by' => $request->updated_by,
            ]);


            // . Create CustomerTransaction only if status changed
            CustomerTransaction::create([
                'customer_id' => $sale->customer_id,
                'sale_id' => $sale->id,
                'type' => 'sale',
                'amount' => $sale->paid_amount,
                'created_by' => $sale->updated_by,
                'updated_by' => $sale->updated_by,
            ]);
            

            // Update customer balances
            $customer = $sale->customer;
            if (strtolower($sale->status->name ?? '') === 'paid') {
                $customer->paid_amount += $sale->total_amount;
                $customer->payable += 0;
            }else{
                $customer->paid_amount += 0;
                $customer->payable += $sale->total_amount;
            }
            $customer->total = $sale->total_amount;
            $customer->save();

            DB::commit();

            // 6️. Return updated sale resource with relationships
            return new SaleResource(
                $sale->fresh(['customer', 'status', 'paymentMethod', 'details.product', 'createdBy', 'updatedBy'])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update sale',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            Sale::findOrFail($id)->delete();
            return response()->json(['message' => 'Sale deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Sale cannot be deleted', 'details' => $e->getMessage()], 400);
        }
    }
}
