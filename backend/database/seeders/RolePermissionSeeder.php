<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{

    public function run(): void
    {
        $adminRole = Role::find(1); // Admin
        // $userRole  = Role::find(2); // User

        // // Admin gets all permissions
        $adminRole->permissions()->sync(range(1, 25));

        // // User gets only view permissions (example)
        // $userRole->permissions()->sync([1, 2, 6, 10, 14]);
    }
}

// php artisan db:seed --class=RolePermissionSeeder