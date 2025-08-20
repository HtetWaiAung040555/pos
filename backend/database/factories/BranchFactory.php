<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            // 'phone' => $this->faker->phoneNumber(),
            'phone' => $this->faker->numerify('09########'),
            // 'location' => $this->faker->city(),
            'location' => $this->faker->address(),
            'status_id' => Status::inRandomOrder()->first()?->id ?? 1,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
