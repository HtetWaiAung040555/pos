<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function index(){
        return User::all();

        // return new UserCollection(User::all());
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function show($id){
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy($id){
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

}
