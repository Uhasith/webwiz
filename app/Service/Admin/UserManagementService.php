<?php

namespace App\Service\Admin;

use App\Repository\Admin\RoleRepo;
use App\Repository\Admin\UserRepo;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Spatie\Permission\Models\Permission;

class UserManagementService
{
    protected $userRepository;
    protected $roleRepository;

    public function __construct(UserRepo $userRepository, RoleRepo $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function getPaginatedUsersWithRoles($perPage = 10)
    {
        $users = $this->userRepository->getPaginatedUsersWithRoles($perPage);
        return UserResource::collection($users);
    }

    public function syncPermissionToRole(int $roleId, int $permissionId, bool $isGranted): bool
    {
        $role = Role::find($roleId);

        if (!$role) {
            throw new \Exception('Role not found');
        }

        $permission = Permission::find($permissionId);

        if (!$permission) {
            throw new \Exception('Permission not found');
        }

        return $this->roleRepository->syncPermission($role, $permissionId, $isGranted);
    }

    public function createRole(array $data)
    {
        // Format the role name: remove spaces and convert to lowercase
        $data['role_name'] = strtolower(str_replace(' ', '', $data['role_name']));

        // If permissions are given as IDs, convert them to names
        if (!empty($data['permissions'])) {
            $data['permissions'] = Permission::whereIn('id', $data['permissions'])
                ->pluck('name')
                ->toArray();
        }

        return $this->roleRepository->saveRole($data);
    }


    public function deleteRole($roleId)
    {
        $role = Role::find($roleId);

        if (!$role) {
            throw new \Exception('Role not found');
        }

        return $this->roleRepository->deleteRole($role);
    }
}
