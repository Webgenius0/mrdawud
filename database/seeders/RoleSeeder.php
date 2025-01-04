<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Define permissions
        $permissions = [
            'User List',
            'Create User',
            'Edit User',
            'Delete User',
            'Event List',
            'Event Edit',
            'Event Delete',
            'Event Create',
            'Role List',
            'Role Edit',
            'Role Delete',
            // Add more permissions as needed
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo($permissions); // Admin gets all permissions

        // Define permissions for agent role
        $agentPermissions = [
            'User List',
            'Create User',
            'Edit User',
            'Event List',
            'Event Edit',
        ];
        $agentRole->givePermissionTo($agentPermissions); // Agent has limited permissions

        // Define permissions for user role
        $userPermissions = [
            'User List',
        ];
        $userRole->givePermissionTo($userPermissions); // User has view-only permissions
    }
}
