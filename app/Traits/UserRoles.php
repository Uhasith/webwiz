<?php

namespace App\Traits;

use App\Models\Role;
use Exception;

trait UserRoles
{
    /**
     * Assign a role to a user.
     *
     * @param \App\Models\User $user
     * @param string $roleName
     * @throws \Exception
     */
    public function assignRoleToUser($user, $roleName)
    {
        $userRole = Role::where('name', $roleName)->first();

        if ($userRole) {
            $user->roles()->attach($userRole->id);
        } else {
            throw new Exception('Role not found');
        }
    }
}