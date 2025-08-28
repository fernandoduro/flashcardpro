<?php

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('card belongs to user', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    expect($card->user)->toBeInstanceOf(User::class);
    expect($card->user->id)->toBe($user->id);
});

test('card belongs to deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    expect($card->deck)->toBeInstanceOf(Deck::class);
    expect($card->deck->id)->toBe($deck->id);
});

test('card has fillable attributes', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $cardData = [
        'user_id' => $user->id,
        'deck_id' => $deck->id,
        'question' => 'Test question?',
        'answer' => 'Test answer',
    ];

    $card = Card::create($cardData);

    expect($card->question)->toBe('Test question?');
    expect($card->answer)->toBe('Test answer');
    expect($card->user_id)->toBe($user->id);
    expect($card->deck_id)->toBe($deck->id);
});

test('card question is required', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    try {
        $card = new Card([
            'user_id' => $user->id,
            'deck_id' => $deck->id,
            'answer' => 'Test answer',
        ]);
        $card->save();
        expect(false)->toBeTrue('Should have thrown an exception for missing question');
    } catch (\Exception $e) {
        expect($e)->toBeInstanceOf(\Exception::class);
    }
});

test('card answer is required', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    try {
        $card = new Card([
            'user_id' => $user->id,
            'deck_id' => $deck->id,
            'question' => 'Test question?',
        ]);
        $card->save();
        expect(false)->toBeTrue('Should have thrown an exception for missing answer');
    } catch (\Exception $e) {
        expect($e)->toBeInstanceOf(\Exception::class);
    }
});

test('card can be deleted', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    $card->delete();

    expect(Card::find($card->id))->toBeNull();
});

test('card has timestamps', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    expect($card->created_at)->not->toBeNull();
    expect($card->updated_at)->not->toBeNull();
    expect($card->created_at)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($card->updated_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('card can be updated', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $card = Card::factory()->for($user)->for($deck)->create([
        'question' => 'Original question?',
        'answer' => 'Original answer',
    ]);

    $card->update([
        'question' => 'Updated question?',
        'answer' => 'Updated answer',
    ]);

    expect($card->fresh()->question)->toBe('Updated question?');
    expect($card->fresh()->answer)->toBe('Updated answer');
});

test('card question can contain special characters', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $card = Card::create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
        'question' => '¿Qué es PHP?',
        'answer' => 'Un lenguaje de programación',
    ]);

    expect($card->question)->toBe('¿Qué es PHP?');
    expect($card->answer)->toBe('Un lenguaje de programación');
});

test('card can have long content', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $longQuestion = str_repeat('This is a long question. ', 50);
    $longAnswer = str_repeat('This is a long answer. ', 30);

    $card = Card::create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
        'question' => $longQuestion,
        'answer' => $longAnswer,
    ]);

    expect(strlen($card->question))->toBeGreaterThan(500);
    expect(strlen($card->answer))->toBeGreaterThan(300);
});
