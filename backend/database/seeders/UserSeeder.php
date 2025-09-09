<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'status_id' => '1',
            'created_by' => '1',
            'updated_by' => '1'
        ]);
    }
}


// php artisan db:seed --class=UserSeeder