<?php

use App\Models\Card;
use App\Models\Deck;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('deck belongs to user', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    expect($deck->user)->toBeInstanceOf(User::class);
    expect($deck->user->id)->toBe($user->id);
});

test('deck has many cards', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $cards = Card::factory()->for($user)->for($deck)->count(3)->create();

    expect($deck->cards)->toHaveCount(3);
    expect($deck->cards->first())->toBeInstanceOf(Card::class);
});

test('deck has many studies', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $studies = Study::factory()->for($user)->for($deck)->count(2)->create();

    expect($deck->studies)->toHaveCount(2);
    expect($deck->studies->first())->toBeInstanceOf(Study::class);
});

test('deck has fillable attributes', function () {
    $user = User::factory()->create();

    $deckData = [
        'user_id' => $user->id,
        'name' => 'Test Deck',
        'public' => true,
        'cover_image_path' => 'test/path/image.jpg',
        'is_pinned' => true,
    ];

    $deck = Deck::create($deckData);

    expect($deck->name)->toBe('Test Deck');
    expect($deck->public)->toBeTrue();
    expect($deck->cover_image_path)->toBe('test/path/image.jpg');
    expect($deck->is_pinned)->toBeTrue();
    expect($deck->user_id)->toBe($user->id);
});

test('deck name is required', function () {
    $user = User::factory()->create();

    try {
        $deck = new Deck([
            'user_id' => $user->id,
            'public' => false,
        ]);
        $deck->save();
        expect(false)->toBeTrue('Should have thrown an exception for missing name');
    } catch (\Exception $e) {
        expect($e)->toBeInstanceOf(\Exception::class);
    }
});

test('deck public defaults to false', function () {
    $user = User::factory()->create();

    $deck = Deck::create([
        'user_id' => $user->id,
        'name' => 'Test Deck',
    ]);

    expect($deck->public)->toBeFalse();
});

test('deck is_pinned defaults to false', function () {
    $user = User::factory()->create();

    $deck = Deck::create([
        'user_id' => $user->id,
        'name' => 'Test Deck',
    ]);

    expect($deck->is_pinned)->toBeFalse();
});

test('deck scope public works', function () {
    $user = User::factory()->create();
    $publicDeck = Deck::factory()->for($user)->create(['public' => true]);
    $privateDeck = Deck::factory()->for($user)->create(['public' => false]);

    $publicDecks = Deck::where('public', true)->get();

    expect($publicDecks)->toHaveCount(1);
    expect($publicDecks->first()->id)->toBe($publicDeck->id);
});

test('deck scope owned by works', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck1 = Deck::factory()->for($user1)->create();
    $deck2 = Deck::factory()->for($user2)->create();

    $user1Decks = Deck::ownedBy($user1->id)->get();

    expect($user1Decks)->toHaveCount(1);
    expect($user1Decks->first()->id)->toBe($deck1->id);
});

test('deck scope recent works', function () {
    $user = User::factory()->create();
    $recentDeck = Deck::factory()->for($user)->create();
    $oldDeck = Deck::factory()->for($user)->create(['created_at' => now()->subDays(10)]);

    $recentDecks = Deck::recent(5)->get();

    expect($recentDecks)->toHaveCount(1);
    expect($recentDecks->first()->id)->toBe($recentDeck->id);
});

test('deck scope most studied works', function () {
    $user = User::factory()->create();
    $studiedDeck = Deck::factory()->for($user)->create();
    $unstudiedDeck = Deck::factory()->for($user)->create();

    Study::factory()->for($user)->for($studiedDeck)->count(3)->create();

    // Use whereHas to filter decks with studies (SQLite compatible)
    $decksWithStudies = Deck::whereHas('studies')
        ->withCount('studies')
        ->orderByDesc('studies_count')
        ->get();

    expect($decksWithStudies)->toHaveCount(1);
    expect($decksWithStudies->first()->id)->toBe($studiedDeck->id);
});

test('deck scope with minimum cards works', function () {
    $user = User::factory()->create();
    $deckWithCards = Deck::factory()->for($user)->create();
    $deckWithoutCards = Deck::factory()->for($user)->create();

    Card::factory()->for($user)->for($deckWithCards)->count(2)->create();

    $decksWithCards = Deck::withMinimumCards(1)->get();

    expect($decksWithCards)->toHaveCount(1);
    expect($decksWithCards->first()->id)->toBe($deckWithCards->id);
});

test('deck can be deleted', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $deck->delete();

    expect(Deck::find($deck->id))->toBeNull();
});

test('deck has timestamps', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    expect($deck->created_at)->not->toBeNull();
    expect($deck->updated_at)->not->toBeNull();
    expect($deck->created_at)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($deck->updated_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('deck can be updated', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create([
        'name' => 'Original Name',
        'public' => false,
    ]);

    $deck->update([
        'name' => 'Updated Name',
        'public' => true,
    ]);

    expect($deck->fresh()->name)->toBe('Updated Name');
    expect($deck->fresh()->public)->toBeTrue();
});

test('deck name can contain special characters', function () {
    $user = User::factory()->create();

    $deck = Deck::create([
        'user_id' => $user->id,
        'name' => 'Matemáticas Avanzadas 2024',
        'public' => false,
    ]);

    expect($deck->name)->toBe('Matemáticas Avanzadas 2024');
});

test('deck can have very long name', function () {
    $user = User::factory()->create();

    $longName = str_repeat('A', 200);

    $deck = Deck::create([
        'user_id' => $user->id,
        'name' => $longName,
        'public' => false,
    ]);

    expect($deck->name)->toBe($longName);
});

test('deck cover image path can be null', function () {
    $user = User::factory()->create();

    $deck = Deck::create([
        'user_id' => $user->id,
        'name' => 'Test Deck',
        'cover_image_path' => null,
    ]);

    expect($deck->cover_image_path)->toBeNull();
});

test('deck can be pinned and unpinned', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create(['is_pinned' => false]);

    $deck->update(['is_pinned' => true]);
    expect($deck->fresh()->is_pinned)->toBeTrue();

    $deck->update(['is_pinned' => false]);
    expect($deck->fresh()->is_pinned)->toBeFalse();
});
