<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Service\Admin\UserManagementService;
use App\Http\Requests\Admin\RoleStoreRequest;
use App\Http\Requests\Admin\SyncPermissionRequest;

class RoleController extends Controller
{
    protected $userManagementService;

    public function __construct(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    public function getRoles()
    {
        $roles = Role::all()->pluck('name', 'id');
        return response()->json($roles);
    }
    
    public function store(RoleStoreRequest $request)
    {
        try {
            $data = $request->only(['role_name', 'permissions']);
            $this->userManagementService->createRole($data);
            $rolesWithPermissions = Role::select('id', 'name')->with('permissions')->orderBy('id')->get();
            $roles = Role::all()->pluck('name', 'id');
            return response()->json(['status' => 'success', 'message' => 'Role created successfully', 'rolesWithPermissions' => $rolesWithPermissions, 'roles' => $roles], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function syncPermissionToRole($roleId, SyncPermissionRequest $request)
    {
        $permissionId = $request->input('permission_id');
        $granted = $request->input('granted');

        try {
            $this->userManagementService->syncPermissionToRole($roleId, $permissionId, $granted);
            return response()->json(['status' => 'success', 'message' => 'Permission assigned successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($roleId)
    {
        try {
            $this->userManagementService->deleteRole($roleId);
            $rolesWithPermissions = Role::select('id', 'name')->with('permissions')->get();
            $roles = Role::all()->pluck('name', 'id');
            return response()->json(['status' => 'success', 'message' => 'Role deleted successfully', 'rolesWithPermissions' => $rolesWithPermissions, 'roles' => $roles], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
