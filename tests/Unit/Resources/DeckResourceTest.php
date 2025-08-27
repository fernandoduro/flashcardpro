<?php

use App\Http\Resources\DeckResource;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Http\Request;

test('deck resource returns correct structure', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Deck',
        'public' => true,
        'is_pinned' => false,
        'cover_image_path' => 'covers/test.jpg',
    ]);

    $resource = new DeckResource($deck);
    $request = new Request;

    $result = $resource->toArray($request);

    expect($result['id'])->toEqual($deck->id);
    expect($result['name'])->toEqual('Test Deck');
    expect($result['is_public'])->toBeTrue();
    expect($result['is_pinned'])->toBeFalse();
    expect($result)->toHaveKey('cover_image_url');
    expect($result)->toHaveKey('cards_count');
    expect($result)->toHaveKey('cards');
    expect($result)->toHaveKey('created_at');
    expect($result)->toHaveKey('updated_at');
});

test('deck resource returns cover image url when path exists', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create([
        'user_id' => $user->id,
        'cover_image_path' => 'covers/test.jpg',
    ]);

    $resource = new DeckResource($deck);
    $request = new Request;

    $result = $resource->toArray($request);

    // The Storage::url() method returns just the path when storage link is not set up
    expect($result['cover_image_url'])->toEqual('covers/test.jpg');
});

test('deck resource returns cover image url when path is null', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create([
        'user_id' => $user->id,
        'cover_image_path' => null,
    ]);

    $resource = new DeckResource($deck);
    $request = new Request;

    $result = $resource->toArray($request);

    // When cover_image_path is null, Storage::url(null) returns '/storage/'
    expect($result['cover_image_url'])->toEqual('/storage/');
});

test('deck resource returns cards count when counted', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    // Create a deck with counted cards (simulating withCount)
    $deckWithCount = Deck::withCount('cards')->find($deck->id);

    $resource = new DeckResource($deckWithCount);
    $request = new Request;

    $result = $resource->toArray($request);

    expect($result['cards_count'])->toEqual(0);
});

test('deck resource returns cards collection when loaded', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    // Create a deck with loaded cards
    $deckWithCards = Deck::with('cards')->find($deck->id);

    $resource = new DeckResource($deckWithCards);
    $request = new Request;

    $result = $resource->toArray($request);

    // When using CardResource::collection(), it returns a ResourceCollection
    // which is an array-like object, not a pure array
    expect($result['cards'])->toBeInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class);
    expect($result['cards'])->toHaveCount(0);
});

test('deck resource returns iso8601 timestamps', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    $resource = new DeckResource($deck);
    $request = new Request;

    $result = $resource->toArray($request);

    expect($result['created_at'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/');
    expect($result['updated_at'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}$/');
});

test('deck resource handles private deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create([
        'user_id' => $user->id,
        'public' => false,
    ]);

    $resource = new DeckResource($deck);
    $request = new Request;

    $result = $resource->toArray($request);

    expect($result['is_public'])->toBeFalse();
});

test('deck resource handles pinned deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create([
        'user_id' => $user->id,
        'is_pinned' => true,
    ]);

    $resource = new DeckResource($deck);
    $request = new Request;

    $result = $resource->toArray($request);

    expect($result['is_pinned'])->toBeTrue();
});
