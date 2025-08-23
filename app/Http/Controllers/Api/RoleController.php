<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    //
    public function index()
    {
        //only if the user has the 'view roles' permission
        if (!Auth::user()?->hasPermissionTo('view role','api')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $roles = Role::with('permissions')->get();
        // Logic to list roles
        return response()->json([
            'message' => 'List of roles',
            'roles' => $roles
        ], 200);
    }

    public function show($id)
    {
        //only if the user has the 'edit roles' permission
        if (!Auth::user()?->hasPermissionTo('edit role','api')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        

        $role = Role::where('id','=', $id)->with('permissions')->firstOrFail();

        //get all permissions
        $permissions = Permission::all();

        // Logic to show a specific role
        return response()->json([
            'message' => 'Role details',
            'role' => $role,
            'permissions' => $permissions
        ], 200);
    }

    public function store(Request $request)
    {
        //only if the user has the 'create roles' permission
        if (!Auth::user()->hasPermissionTo('create role','api')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        //validate the role name and array of permission ids
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            //'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);

        if ($request->has('permissions')) {
            
            $role->givePermissionTo($request->permissions);
        }
        // Logic to create a new role
        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    public function update(Request $request, $id)
    {
        //only if the user has the 'edit roles' permission
        if (!Auth::user()->hasPermissionTo('edit role','api')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        //validate the role name
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        // Logic to update a role
        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ], 200);
    }

    public function destroy($id)
    {
        //only if the user has the 'delete roles' permission
        if (!Auth::user()->hasPermissionTo('delete role','api')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $role = Role::find($id);
        $role->delete();
        // Logic to delete a role
        return response()->json([
            'message' => 'Role deleted successfully'
        ], 200);
    }
}
