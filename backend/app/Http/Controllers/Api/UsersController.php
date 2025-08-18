<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UsersController extends Controller
{
    public function index(){
        $users = User::with(['branch', 'counter', 'status', 'roles', 'createdBy', 'updatedBy'])->get();
        return UserResource::collection($users);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'branch_id'  => 'nullable|exists:branches,id',
            'counter_id' => 'nullable|exists:counters,id',
            'status_id'  => 'nullable|exists:statuses,id',
            'created_by' => 'nullable|exists:users,id',
            'roles' => 'array|exists:roles,id'
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'branch_id'  => $request->branch_id,
            'counter_id' => $request->counter_id,
            'status_id'  => $request->status_id,
            'created_by' => $request->created_by,
            'updated_by' => $request->updated_by ?? $request->created_by,
        ]);

        if ($request->roles) {
            $user->roles()->attach($request->roles);
        }

        return new UserResource($user->fresh(['branch', 'counter', 'status', 'createdBy', 'updatedBy']));
    }

    public function show($id){
        $user = User::with(['branch','counter','status','roles','createdBy','updatedBy'])->findOrFail($id);
        return new UserResource($user);
    }

    public function update(Request $request, $id){

        $user = User::findOrFail($id);

        $request->validate([
            'name'       => 'sometimes|string|max:255',
            'email'      => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password'   => 'sometimes|string|min:8',
            'branch_id'  => 'exists:branches,id',
            'counter_id' => 'nullable|exists:counters,id',
            'status_id'  => 'exists:statuses,id',
            'updated_by' => 'required|exists:users,id',
            'roles'      => 'sometimes|array|exists:roles,id',
        ]);

        $data = $request->only(['name', 'email', 'password', 'branch_id', 'counter_id', 'status_id']);

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        // Sync roles if provided
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return new UserResource($user->fresh(['branch','counter','status','roles','createdBy','updatedBy']));
    }

    public function destroy($id){
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function assignRole(User $user, Role $role)
    {
        $user->roles()->attach($role->id);
        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $user->load('roles')
        ]);
    }

    public function removeRole(User $user, Role $role)
    {
        $user->roles()->detach($role->id);
        return response()->json([
            'message' => 'Role removed successfully',
            'user' => $user->load('roles')
        ]);
    }

    public function hasPermission(User $user, Permission $permission)
    {
        $hasPermission = $user->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->contains('id', $permission->id);

        return response()->json([
            'user_id' => $user->id,
            'permission_id' => $permission->id,
            'has_permission' => $hasPermission
        ]);
    }

}
