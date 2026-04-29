<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition(): array
    {
        return [
            'text' => $this->faker->sentence(),
            'correct' => $this->faker->boolean(),
            'question_id' => Question::factory(),
        ];
    }
}
