<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'quiz_id' => Quiz::factory(),
            'result' => fake()->numberBetween(0, 100),
            'ip_address' => fake()->ipv4(),
            'time_spent' => fake()->numberBetween(60, 3600),
        ];
    }
}
