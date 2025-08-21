<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeckFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'public' => $this->faker->boolean(20), // 20% chance of being public
        ];
    }
}