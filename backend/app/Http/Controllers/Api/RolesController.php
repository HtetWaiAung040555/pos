<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{

    public function index()
    {
        $roles = Role::with(['status', 'createdBy', 'updatedBy'])->get();
        return RoleResource::collection($roles);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string|max:1000',
            'status_id' => 'required|exists:statuses,id',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'status_id' => $request->status_id,
            'created_by' => $request->created_by,
            'updated_by' => $request->updated_by ?? $request->created_by,
        ]);

        return new RoleResource($role->fresh(['status', 'createdBy', 'updatedBy']));
    }


    public function show(string $id)
    {
        $role = Role::with(['status', 'createdBy', 'updatedBy'])->findOrFail($id);
        return new RoleResource($role);
    }


    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'desc' => 'nullable|string|max:1000',
            'status_id' => 'sometimes|required|exists:statuses,id',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        $data = $request->only(['name', 'desc', 'status_id', 'updated_by']);
        $role->update($data);

        return new RoleResource($role->fresh(['status', 'createdBy', 'updatedBy']));
    }


    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(null, 204);
    }
}
