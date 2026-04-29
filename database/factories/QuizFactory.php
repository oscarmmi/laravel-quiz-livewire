<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'published' => fake()->boolean(),
            'public' => fake()->boolean(),
            'created_by' => \App\Enums\TestCreatedBy::Admin,
        ];
    }
}
