<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    //
    public function index()
    {
        //only if the user has the 'view permission' permission
        if (!Auth::user()->hasPermissionTo('view permission', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $permissions = Permission::with('roles')->get();
        // Logic to list permissions
        return response()->json([
            'message' => 'List of permissions',
            'permissions' => $permissions
        ], 200);
    }

    public function show($id)
    {
        //only if the user has the 'edit permission' permission
        if (!Auth::user()->hasPermissionTo('edit permission', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $permission = Permission::where('id', '=', $id)->with('roles')->firstOrFail();
        // Logic to show a specific permission
        return response()->json([
            'message' => 'Permission details',
            'permission' => $permission
        ], 200);
    }

    public function store(Request $request)
    {
        //only if the user has the 'create permission' permission
        if (!Auth::user()->hasPermissionTo('create permission', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $request->name, 'guard_name' => 'api']);
        // Logic to create a new permission
        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $permission
        ], 201);
    }

    public function update(Request $request, $id)
    {
        //only if the user has the 'edit permission' permission
        if (!Auth::user()->hasPermissionTo('edit permission', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->save();
        // Logic to update a permission
        return response()->json([
            'message' => 'Permission updated successfully',
            'permission' => $permission
        ], 200);
    }

    public function destroy($id)
    {
        //only if the user has the 'delete permission' permission
        if (!Auth::user()->hasPermissionTo('delete permission', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $permission = Permission::findOrFail($id);
        $permission->delete();
        // Logic to delete a permission
        return response()->json([
            'message' => 'Permission deleted successfully'
        ], 200);
    }
}
