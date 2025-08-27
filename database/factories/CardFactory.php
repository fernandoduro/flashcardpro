<?php

namespace Database\Factories;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'deck_id' => Deck::factory(),
            'question' => rtrim($this->faker->sentence(), '.').'?',
            'answer' => $this->faker->paragraph(),
        ];
    }
}
