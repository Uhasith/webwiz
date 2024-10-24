<?php

namespace App\Repository\Admin;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRepo
{
    public function getPaginatedUsersWithRoles($perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return User::with('roles')->paginate($perPage);
    }

    public function getUsersWithRoles(): array
    {
        $query = User::with('roles')->get();

        return [
            'total' => $query->count(),
            'data' => $query->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact' => $user->contact ?? 'No contact',
                    'roles' => $user->roles,
                ];
            }),
        ];
    }

    public function getRolesWithPermissions(): \Illuminate\Database\Eloquent\Collection|array
    {
        return Role::with('permissions')->get();
    }

    public function findUserData($id){
        return User::findOrFail($id);
    }

    public function getUserByEmail($email){

        return User::where('email', $email)
        ->whereNull('deleted_at')
        ->first();
    }

    public function save(User $user): void
    {
        $user->save();
    }

    public function getUserByOTP($email,$otp){
        return User::where('email', $email)
        ->where('otp',$otp)
        ->whereNull('deleted_at')
        ->first();
    }

}
