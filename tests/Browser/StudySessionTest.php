<?php

namespace Tests\Browser;

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('a user can complete a study session', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $card = Card::factory()->create(['deck_id' => $deck->id, 'user_id' => $user->id]);

    $this->browse(function (Browser $browser) use ($user, $deck, $card) {
        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Sign in')
            ->waitForLocation(route('decks.index'));

        $token = $browser->script("return localStorage.getItem('api_token');")[0];

        expect($token)->not->toBeNull();

        $browser->visit(route('study.show', $deck));

        $browser->screenshot('study-session-before-wait');

        $browser->waitForText($card->question, 15)
            ->assertSee($card->question)
            ->click('button.bg-primary-600') // Reveal Answer
            ->waitForText($card->answer, 5)
            ->assertSee($card->answer)
            ->click('button.bg-green-500') // I Got It Right
            ->waitForText('Session Complete!', 10)
            ->assertSee('You got 1 out of 1 correct.');
    });
});
