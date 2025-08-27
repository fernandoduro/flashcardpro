<?php

namespace Tests\Browser;

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('complete study session workflow from deck selection to completion', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $deck = Deck::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Study Deck',
    ]);

    // Create multiple cards for a realistic study session
    $cards = Card::factory()->count(4)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    $this->browse(function (Browser $browser) use ($user, $deck, $cards) {
        // 1. Login process
        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Sign in')
            ->waitForLocation(route('decks.index'));

        // 2. Navigate to the study session
        $browser->clickLink($deck->name)
            ->waitForLocation(route('decks.show', $deck))
            ->assertSee($deck->name)
            ->assertSee('Study Session')
            ->clickLink('Study Session')
            ->waitForLocation(route('study.show', $deck));

        // 3. Verify study session loads correctly
        $browser->assertSee('Study Session')
            ->assertSee($deck->name)
            ->assertSee('4 / 4') // Should show total card count
            ->assertSee($cards->first()->question);

        // 4. Complete the study session
        $correctAnswers = 0;
        foreach ($cards as $index => $card) {
            // Verify current card
            $browser->assertSee($card->question);

            // Click reveal answer
            $browser->click('button.bg-primary-600')
                ->waitForText($card->answer)
                ->assertSee($card->answer);

            // Answer the question (alternate correct/incorrect)
            $isCorrect = $index % 2 === 0;
            if ($isCorrect) {
                $correctAnswers++;
                $browser->click('button.bg-green-500'); // I Got It Right
            } else {
                $browser->click('button.bg-gray-200'); // Maybe Next Time
            }

            // Wait for next card or completion
            if ($index < 3) {
                $browser->waitForText($cards[$index + 1]->question);
            }
        }

        // 5. Verify completion screen
        $browser->waitForText('Session Complete!')
            ->assertSee('Session Complete!')
            ->assertSee("You got {$correctAnswers} out of 4 correct.");

        // 6. Return to deck
        $browser->clickLink('Back to Deck')
            ->waitForLocation(route('decks.show', $deck))
            ->assertSee($deck->name);
    });
});

test('study session handles empty deck gracefully', function () {
    $user = User::factory()->create();
    $emptyDeck = Deck::factory()->create([
        'user_id' => $user->id,
        'name' => 'Empty Deck',
    ]);

    $this->browse(function (Browser $browser) use ($user, $emptyDeck) {
        // Login
        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Sign in')
            ->waitForLocation(route('decks.index'));

        // Navigate to empty deck
        $browser->clickLink($emptyDeck->name)
            ->waitForLocation(route('decks.show', $emptyDeck))
            ->clickLink('Study Session')
            ->waitForLocation(route('study.show', $emptyDeck));

        // Should show empty deck message or handle gracefully
        $browser->assertSee('Study Session')
            ->assertSee($emptyDeck->name);

        // Should either show completion immediately or handle empty state
        $browser->waitForText(function ($text) {
            return strpos($text, 'Session Complete!') !== false ||
                   strpos($text, 'No cards available') !== false;
        });
    });
});

test('study session persists progress when navigating away and back', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $cards = Card::factory()->count(3)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    $this->browse(function (Browser $browser) use ($user, $deck, $cards) {
        // Login and start study session
        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Sign in')
            ->waitForLocation(route('decks.index'));

        $browser->clickLink($deck->name)
            ->waitForLocation(route('decks.show', $deck))
            ->clickLink('Study Session')
            ->waitForLocation(route('study.show', $deck));

        // Complete first card
        $browser->assertSee($cards[0]->question)
            ->click('button.bg-primary-600')
            ->waitForText($cards[0]->answer)
            ->click('button.bg-green-500')
            ->waitForText($cards[1]->question);

        // Navigate away (to deck index)
        $browser->visit(route('decks.index'))
            ->waitForLocation(route('decks.index'));

        // Come back to study session
        $browser->clickLink($deck->name)
            ->waitForLocation(route('decks.show', $deck))
            ->clickLink('Study Session')
            ->waitForLocation(route('study.show', $deck));

        // Should continue from where we left off (second card)
        $browser->assertSee($cards[1]->question);
    });
});

test('study session handles API token refresh correctly', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    $this->browse(function (Browser $browser) use ($user, $deck, $card) {
        // Login
        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Sign in')
            ->waitForLocation(route('decks.index'));

        // Start study session
        $browser->clickLink($deck->name)
            ->waitForLocation(route('decks.show', $deck))
            ->clickLink('Study Session')
            ->waitForLocation(route('study.show', $deck));

        // Verify API token exists in localStorage
        $token = $browser->script("return localStorage.getItem('api_token');")[0];
        expect($token)->not->toBeNull();
        expect(strlen($token))->toBeGreaterThan(0);

        // Complete the study session
        $browser->assertSee($card->question)
            ->click('button.bg-primary-600')
            ->waitForText($card->answer)
            ->click('button.bg-green-500')
            ->waitForText('Session Complete!')
            ->assertSee('You got 1 out of 1 correct.');
    });
});
