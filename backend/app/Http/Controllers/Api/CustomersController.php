<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
     public function index()
    {
        $customers = Customer::with(['status','createdBy', 'updatedBy'])->get();
        return CustomerResource::collection($customers);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'status_id' => 'required|exists:statuses,id',
            'is_default' => 'boolean',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ]);
    
        $Customer = Customer::create([
            'id' => $request->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'status_id' => $request->status_id,
            'is_default' => $request->is_default ?? false,
            'created_by' => $request->created_by,
            'updated_by' => $request->updated_by ?? $request->created_by,
        ]);
    
        return new CustomerResource($Customer->fresh(['status', 'createdBy', 'updatedBy']));
    }

 
    public function show(string $id)
    {
        $Customer = Customer::with(['status', 'createdBy', 'updatedBy'])->findOrFail($id);
        return new CustomerResource($Customer);
    }


    public function update(Request $request, string $id)
    {
        $Customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|string|max:50',
            'address' => 'sometimes|string|max:255',
            'status_id' => 'sometimes|required|exists:statuses,id',
            'is_default' => 'sometimes|boolean',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        $data = $request->only(['name', 'phone', 'address', 'status_id', 'updated_by']);

        $Customer->update($data);

        return new CustomerResource($Customer->fresh(['status', 'createdBy', 'updatedBy']));
    }

    
    public function destroy(string $id)
    {
        try {
            Customer::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Customer cannot be deleted'], 400);
        }
    }

    public function getLastId()
    {
        // Get the last customer by creation time (or by ID descending)
        $lastCustomer = Customer::orderBy('id', 'desc')->first();

        if ($lastCustomer) {
            $lastId = $lastCustomer->id;
        } else {
            $lastId = null;
        }

        return response()->json(['last_id' => $lastId]);
    }

}
