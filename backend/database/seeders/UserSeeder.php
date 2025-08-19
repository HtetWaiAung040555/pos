<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run():void{
        User::factory(50)->create();
    }
}


// php artisan db:seed --class=UserSeeder