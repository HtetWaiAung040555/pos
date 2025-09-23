<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\File;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::with(['createdBy', 'updatedBy'])->get();
        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'unit'       => 'nullable|string|max:255',
            'sec_prop'   => 'nullable|string|max:255',
            'price'      => 'required|numeric|min:0',
            'barcode'    => 'nullable|string|max:255|unique:products,barcode',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        // Create product first (to get ID)
        $product = Product::create([
            'name'       => $request->name,
            'unit'       => $request->unit,
            'sec_prop'   => $request->sec_prop,
            'price'      => $request->price,
            'barcode'    => $request->barcode,
            'created_by' => $request->created_by,
            'updated_by' => $request->updated_by ?? $request->created_by,
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fname = $file->getClientOriginalName();
            $user_id = $request->created_by;
            $imagenewname = uniqid($user_id) . '_' . $product->id . '_' . $fname;

            $file->move(public_path('assets/img/products/'), $imagenewname);

            $product->image = 'assets/img/products/' . $imagenewname;
            $product->save();
        }

        return new ProductResource($product->fresh(['createdBy','updatedBy']));
    }

    public function show(string $id)
    {
        $product = Product::with(['createdBy','updatedBy'])->findOrFail($id);
        return new ProductResource($product);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'       => 'sometimes|required|string|max:255',
            'unit'       => 'nullable|string|max:255',
            'sec_prop'   => 'nullable|string|max:255',
            'price'      => 'sometimes|required|numeric|min:0',
            'barcode'    => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        $data = $request->only(['name','unit','sec_prop','price','barcode','updated_by']);
        $user_id = $request->updated_by ?? $product->created_by;

        // Remove old image if exists
        if ($request->hasFile('image') && $product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        // Upload new image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fname = $file->getClientOriginalName();
            $imagenewname = uniqid($user_id) . '_' . $product->id . '_' . $fname;

            $file->move(public_path('assets/img/products/'), $imagenewname);
            $data['image'] = 'assets/img/products/' . $imagenewname;
        }

        $product->update($data);

        return new ProductResource($product->fresh(['createdBy','updatedBy']));
    }

    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete image
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            $product->delete();

            return response()->json(['message' => 'Deleted Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Product cannot be deleted'], 400);
        }
    }
}
