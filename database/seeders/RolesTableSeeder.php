<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        // Define roles
        $superadmin = Role::updateOrCreate(['name' => 'superadmin'], ['name' => 'superadmin']);
        $admin = Role::updateOrCreate(['name' => 'admin'], ['name' => 'admin']);
//        $user = Role::updateOrCreate(['name' => 'user'], ['name' => 'user']);

        // Define permissions with category
        $permissions = [
            ['name' => 'Create Sub Admin', 'category' => 'user_management'],
            ['name' => 'Create Permission', 'category' => 'permission_management'],
            ['name' => 'Create Role', 'category' => 'permission_management'],
            ['name' => 'Create Report', 'category' => 'report_management'],
            ['name' => 'Create Template', 'category' => 'report_management'],
            ['name' => 'Create Notification', 'category' => 'notification_management'],
            ['name' => 'View Sub Admin', 'category' => 'user_management'],
            ['name' => 'View Report', 'category' => 'report_management'],
            ['name' => 'View Role', 'category' => 'permission_management'],
            ['name' => 'View Template', 'category' => 'report_management'],
            ['name' => 'View Equipment', 'category' => 'equipment_management'],
            ['name' => 'View Sensor Data', 'category' => 'sensors_data_management'],
            ['name' => 'View Permission', 'category' => 'permission_management'],
            ['name' => 'View Notification', 'category' => 'notification_management'],
            ['name' => 'Update Sensor Data', 'category' => 'sensors_data_management'],
            ['name' => 'Update Equipment', 'category' => 'equipment_management'],
            ['name' => 'Update Template', 'category' => 'report_management'],
            ['name' => 'Update Report', 'category' => 'report_management'],
            ['name' => 'Update Role', 'category' => 'permission_management'],
            ['name' => 'Update Sub Admin', 'category' => 'user_management'],
            ['name' => 'Update Permission', 'category' => 'permission_management'],
            ['name' => 'Data Export', 'category' => 'data_export_management'],
        ];

        // Remove all current permissions from roles
        $superadmin->syncPermissions([]);
        $admin->syncPermissions([]);
//        $user->syncPermissions([]);

        // Seed permissions
        $permissionIds = [];
        foreach ($permissions as $perm) {
            $permission = Permission::updateOrCreate(
                ['name' => $perm['name']],
                ['category' => $perm['category']]
            );
            $permissionIds[] = $permission->id;
        }

        // Assign all permissions to superadmin and admin
        $superadmin->syncPermissions($permissionIds);
        $admin->syncPermissions($permissionIds);

        // Assign specific permission to user role
//        $viewReportPermission = Permission::where('name', 'View Report')->first();
//        if ($viewReportPermission) {
//            $user->syncPermissions([$viewReportPermission->id]);
//        }
    }
}
