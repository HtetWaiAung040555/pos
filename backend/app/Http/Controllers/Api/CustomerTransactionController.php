<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerTransactionResource;
use App\Models\CustomerTransaction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerTransaction::with(['customer', 'paymentMethod', 'createdBy', 'updatedBy']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_id')) {
            $query->where('payment_id', $request->payment_id);
        }

        return CustomerTransactionResource::collection(
            $query->orderBy('id', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        Log::info($request->all());

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'amount' => 'required|numeric|min:0',
            'payment_id' => 'nullable|exists:payment_methods,id',
            'remark' => 'nullable|string|max:2000',
            'pay_date' => 'required|date',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();

        try {

            // 1. Create transaction
            $transaction = CustomerTransaction::create([
                'customer_id' => $request->customer_id,
                'type' => 'payment',
                'amount' => $request->amount,
                'payment_id' => $request->payment_id,
                'remark' => $request->remark,
                'pay_date' => $request->pay_date,
                'created_by' => $request->created_by,
                'updated_by' => $request->updated_by ?? $request->created_by,
            ]);

            // 2. Update customer balance
            $this->updateCustomerBalance($transaction->customer_id);

            DB::commit();

            return new CustomerTransactionResource(
                $transaction->load(['customer', 'paymentMethod', 'createdBy', 'updatedBy'])
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create transaction',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $transaction = CustomerTransaction::with(['customer', 'paymentMethod', 'createdBy', 'updatedBy'])
            ->findOrFail($id);

        return new CustomerTransactionResource($transaction);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'payment_id' => 'sometimes|exists:payment_methods,id',
            'remark' => 'nullable|string|max:2000',
            'pay_date' => 'sometimes|date',
            'updated_by' => 'required|exists:users,id',
        ]);

        $transaction = CustomerTransaction::findOrFail($id);

        DB::beginTransaction();
        try {

            // 1. Update fields
            $transaction->fill($request->only(['amount','payment_id','remark','pay_date']));
            $transaction->updated_by = $request->updated_by;
            $transaction->save();

            // 2. Recalculate customer balance
            $this->updateCustomerBalance($transaction->customer_id);

            DB::commit();

            return new CustomerTransactionResource(
                $transaction->load(['customer', 'paymentMethod', 'createdBy', 'updatedBy'])
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update transaction',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $transaction = CustomerTransaction::findOrFail($id);
        $customerId = $transaction->customer_id;

        DB::beginTransaction();
        try {

            $transaction->delete();

            // Recalculate customer balance after delete
            $this->updateCustomerBalance($customerId);

            DB::commit();

            return response()->json(['message' => 'Transaction deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Cannot delete transaction',
                'details' => $e->getMessage()
            ], 400);
        }
    }

    // Customer Balance
    private function updateCustomerBalance($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $paid = CustomerTransaction::where('customer_id', $customerId)
            ->where('type', 'payment')
            ->sum('amount');

        $customer->payable = $customer->total - $paid;

        $customer->save();
    }
}
