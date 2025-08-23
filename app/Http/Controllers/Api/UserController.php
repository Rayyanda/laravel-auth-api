<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        //only if the user has the 'view users' permission
        // if (!Auth::user()->hasPermissionTo('view users', 'api')) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $users = \App\Models\User::with('roles')->get();
        // Logic to list users
        return response()->json([
            'message' => 'List of users',
            'users' => $users
        ], 200);
    }

    public function show($id)
    {
        //only if the user has the 'view user' permission
        // if (!Auth::user()->hasPermissionTo('view users', 'api')) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $user = \App\Models\User::where('id','=',$id)->with('roles')->first();
        // Logic to show a specific user
        return response()->json([
            'message' => 'User details',
            'user' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        //only if the user has the 'edit user' permission
        // if (!Auth::user()->hasPermissionTo('edit users', 'api')) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }
        //validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'roles' => 'array',
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->update($request->only('name', 'email'));
        //sync roles if provided
        $user->syncRoles($request->roles ?? []);
        // Logic to update a user
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }

    public function destroy($id)
    {
        //only if the user has the 'delete user' permission
        if (!Auth::user()->hasPermissionTo('delete users', 'api')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        // Logic to delete a user
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
