<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::insert([
            [
                'name' => 'Main',
                'phone' => '09987654321,09123456789,09112233445',
                'location' => 'Mandalay',
                'status_id' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2025-08-20 02:55:06',
                'updated_at' => '2025-08-20 02:55:06',
            ],
            [
                'name' => 'Secondary',
                'phone' => '09987654321,09123456789,09112233445',
                'location' => 'Yangon',
                'status_id' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2025-08-18 10:26:38',
                'updated_at' => '2025-08-18 10:26:38',
            ],
        ]);
    }
}

// php artisan db:seed --class=BranchSeeder