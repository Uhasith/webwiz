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
                'name' => 'Super Admin User',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => Utility::$superAdmin,
                'status' => Utility::$statusActive,
            ],
//            [
//                'name' => 'Admin User',
//                'email' => 'admin@example.com',
//                'password' => Hash::make('gtadmin1@su!olk'),
//                'role' => Utility::$admin,
//                'status' => Utility::$statusActive
//            ],
//            [
//                'name' => 'Normal User',
//                'email' => 'user@example.com',
//                'password' => Hash::make("gtuser1@su!olk"),
//                'role' => Utility::$user,
//                'status' => Utility::$statusActive
//            ],
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
