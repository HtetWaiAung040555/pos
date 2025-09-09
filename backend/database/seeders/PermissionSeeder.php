<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['id' => 1, 'name' => 'Dashboard', 'action' => 'View', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 2, 'name' => 'Sales', 'action' => 'View', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 3, 'name' => 'Sales', 'action' => 'Create', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 4, 'name' => 'Sales', 'action' => 'Update', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 5, 'name' => 'Sales', 'action' => 'Delete', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 6, 'name' => 'Purchase', 'action' => 'View', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 7, 'name' => 'Purchase', 'action' => 'Create', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 8, 'name' => 'Purchase', 'action' => 'Update', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 9, 'name' => 'Purchase', 'action' => 'Delete', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 10, 'name' => 'Branch', 'action' => 'View', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 11, 'name' => 'Branch', 'action' => 'Create', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 12, 'name' => 'Branch', 'action' => 'Update', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 13, 'name' => 'Branch', 'action' => 'Delete', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 14, 'name' => 'Counter', 'action' => 'View', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 15, 'name' => 'Counter', 'action' => 'Create', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 16, 'name' => 'Counter', 'action' => 'Update', 'created_by' => 1, 'updated_by' => 1],
            ['id' => 17, 'name' => 'Counter', 'action' => 'Delete', 'created_by' => 1, 'updated_by' => 1],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['id' => $permission['id']],
                $permission
            );
        }
    }
}

// php artisan db:seed --class=PermissionSeeder