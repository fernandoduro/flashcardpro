<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => rtrim($this->faker->sentence(), '.') . '?',
            'answer' => $this->faker->paragraph(),
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
        ];
    }
}