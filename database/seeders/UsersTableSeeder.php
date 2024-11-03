<?php

namespace Database\Seeders;

use App\Helpers\Utility;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Define users with their roles
        $users = [
            [
                'name' => 'SuperAdmin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('32#6@sLjl0k'),
                'role' => Utility::$superAdmin,
                'status' => Utility::$statusActive,
            ]
        ];

        // Seed users and assign roles
        foreach ($users as $userData) {
            $user = User::updateOrCreate([
                'email' => $userData['email'],
            ], [
                'name' => $userData['name'],
                'status' => $userData['status'],
                'password' => $userData['password'],
            ]);

            $user->syncRoles([]);
            $role = Role::findByName($userData['role']);
            if ($role) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
