<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StatusSeeder::class,
            BranchSeeder::class,
            CounterSeeder::class,
            UserSeeder::class, // optional if you create a separate seeder for users
        ]);
    }
}

// php artisan db:seed