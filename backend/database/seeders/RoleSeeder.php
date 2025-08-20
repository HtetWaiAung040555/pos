<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin', 'Manager', 'Employee'];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'status_id' => Status::inRandomOrder()->first()?->id ?? 1,
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
