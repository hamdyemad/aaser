<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\AdminRole;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Requests\AdminRoleRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;

class RolePermissionController extends Controller
{
    public function addPermission(PermissionRequest $request)
    {
        Permission::create([
            'name' => $request->name
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Permission Added Successfully',
        ]);
    }

    public function deletePermission($id)
    {
        $permission = Permission::findorFail($id);
        $permission->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Permission Deleted Successfully',
        ]);
    }

    public function allPermission()
    {
        $permissions = Permission::all();
        return response()->json([
            'status' => 'Success',
            'data' => PermissionResource::collection($permissions),
            'message' => 'Permissions Returned Successfully',
        ]);
    }

    public function addRole(RoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name
        ]);
        foreach($request->permissions as $permission)
        {
            RolePermission::create([
                'role_id' => $role->id,
                'permission_id' => $permission,
            ]);
        }
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Role Added Successfully',
        ]);
    }

    public function editRole(RoleRequest $request, $id)
    {
        $role = Role::findorFail($id);
        $role->update([
            'name' => $request->name
        ]);
        $role->rolePermission()->delete();
        foreach($request->permissions as $permission)
        {
            RolePermission::create([
                'role_id' => $role->id,
                'permission_id' => $permission,
            ]);
        }
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Role Edited Successfully',
        ]);
    }

    public function deleteRole($id)
    {
        $role = Role::findorFail($id);
        $role->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Role Deleted Successfully',
        ]);
    }

    public function viewRole($id)
    {
        $role = Role::with('rolePermission')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new RoleResource($role),
            'message' => 'Role Returned Successfully',
        ]);
    }

    public function allRole()
    {
        $roles = Role::all();
        return response()->json([
            'status' => 'Success',
            'data' => RoleResource::collection($roles),
            'message' => 'Roles Returned Successfully',
        ]);
    }

    public function assignAdminRole(AdminRoleRequest $request)
    {
        AdminRole::create([
            'role_id' => $request->role_id,
            'admin_id' => $request->admin_id,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Admin Role Added Successfully',
        ]);
    }

    public function viewAdminRole($id)
    {
        $admin_role = AdminRole::where('admin_id',$id)->first();
        $role = Role::with('rolePermission')->findorFail($admin_role->role_id);
        return response()->json([
            'status' => 'Success',
            'data' => new RoleResource($role),
            'message' => 'Role Returned Successfully',
        ]);
    }
}
