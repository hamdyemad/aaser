<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;
use App\Http\Requests\AddAdminRequest;
use App\Http\Requests\EditAdminRequest;
use App\Http\Requests\LoginAdminRequest;

class AdminController extends Controller
{
    public function login(LoginAdminRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => "Fail",
                'data' => null,
                'message' => 'Invalid Email Or Password'
            ], 422);
        }
        $token = $admin->createToken($admin->name);
        $admin_role = AdminRole::where('admin_id',$admin->id)->first();
        $role = Role::with('rolePermission')->findorFail($admin_role->role_id);
        return response()->json([
            'token' => $token->plainTextToken,
            'status' => "Success",
            'data' => new RoleResource($role),
            'message' => 'Login Successfully'
        ]);
    }

    public function getProfile(Request $request)
    {
        $auth = $request->user();
        return response()->json([
            'status' => 'Success',
            'data' => new AdminResource(Admin::findorFail($auth->id)),
            'message' => 'Profile Returned Successfully',
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Logout Successfully',
        ]);
    }

    public function edit(EditAdminRequest $request)
    {
        $auth = $request->user();
        $admin = Admin::findorFail($auth->id);
        $password = $request->password ? Hash::make($request->password) : $admin->password;
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'phone' => $request->phone,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Profile Edit Successfully',
        ]);
    }

    public function addAdmin(AddAdminRequest $request)
    {
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        AdminRole::create([
            'role_id' => $request->role_id,
            'admin_id' => $admin->id,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Admin Added Successfully',
        ]);
    }

    public function editAdmin(EditAdminRequest $request, $id)
    {
        $admin = Admin::findorFail($id);
        $password = $request->password ? Hash::make($request->password) : $admin->password;
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $password,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Admin Edited Successfully',
        ]);
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::findorFail($id);
        $admin->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Admin Deleted Successfully',
        ]);
    }

    public function showAdmin($id)
    {
        $admin = Admin::findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new AdminResource($admin),
            'message' => 'Admin Returned Successfully',
        ]);
    }

    public function allAdmins(Request $request)
    {
        $item = $request->item ?? 20;
        $admins = Admin::when($request->filled('name'),function($query) use($request){
            $query->where('name', 'like', "%{$request->name}%");
        })->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => AdminResource::collection($admins),
            'message' => 'Admins Returned Successfully',
            'pagination' => [
                'current_page' => $admins->currentPage(),
                'last_page' => $admins->lastPage(),
                'per_page' => $admins->perPage(),
                'total' => $admins->total(),
            ],
        ]);
    }
}
