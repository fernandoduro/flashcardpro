<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
     public function run(): void
    {
        // Create 2 specific users
        $user1 = User::factory()->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
        ]);

        // Create decks and cards for Alice
        Deck::factory(3)
            ->for($user1)
            ->has(Card::factory()->count(10)->for($user1))
            ->create();

        // Create decks and cards for Bob
        Deck::factory(2)
            ->for($user2)
            ->has(Card::factory()->count(15)->for($user2))
            ->create();
    }
}
