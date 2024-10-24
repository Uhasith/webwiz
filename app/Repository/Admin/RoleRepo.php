<?php

namespace App\Repository\Admin;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

/**
 * Class RoleRepo.
 */
class RoleRepo  
{
    public function syncPermission(Role $role, int $permissionId, bool $isGranted): bool
    {
        if ($isGranted) {
            // Assign the permission to the role
            $role->givePermissionTo($permissionId);
        } else {
            // Remove the permission from the role
            $role->revokePermissionTo($permissionId);
        }

        return true;
    }

    public function saveRole(array $data)
    {
        DB::beginTransaction();

        try {
            // Create a new role with the given name
            $role = Role::create(['name' => $data['role_name'], 'guard_name' => 'web']);

            // Assign permissions if provided
            if (!empty($data['permissions'])) {
                Log::info($data['permissions']);
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteRole(Role $role)
    {
        $role->delete();
    }
}
