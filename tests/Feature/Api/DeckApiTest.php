<?php

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

// This applies the RefreshDatabase trait to all tests in this file.
uses(RefreshDatabase::class);

test('user can retrieve their own decks', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    Card::factory()->count(3)->create(['deck_id' => $deck->id, 'user_id' => $user->id]);

    // `actingAs($user, 'sanctum')` is a clean way to authenticate API requests.
    actingAs($user, 'sanctum')
        ->getJson('/api/v1/decks')
        ->assertOk() // Equivalent to assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'pagination' => [
                'current_page',
                'total',
                'per_page',
            ],
            'api_version',
            'timestamp',
        ])
        ->assertJsonCount(1, 'data');
});

test('user cannot retrieve other users decks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    Deck::factory()->create(['user_id' => $user2->id]);

    actingAs($user1, 'sanctum')
        ->getJson('/api/v1/decks')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

test('user can retrieve cards from their own deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    Card::factory()->count(3)->create([
        'deck_id' => $deck->id,
        'user_id' => $user->id,
    ]);

    actingAs($user, 'sanctum')
        ->getJson("/api/v1/decks/{$deck->id}/cards")
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'api_version',
            'timestamp',
        ])
        ->assertJsonCount(3, 'data');
});

test('user cannot retrieve cards from other users deck', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user2->id]);

    actingAs($user1, 'sanctum')
        ->getJson("/api/v1/decks/{$deck->id}/cards")
        ->assertForbidden(); // Equivalent to assertStatus(403)
});

test('user can create card in their own deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    $cardData = [
        'question' => 'What is the capital of France?',
        'answer' => 'Paris',
    ];

    actingAs($user, 'sanctum')
        ->postJson("/api/v1/decks/{$deck->id}/cards", $cardData)
        ->assertCreated() // Equivalent to assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'question',
                'answer',
            ],
            'api_version',
            'timestamp',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Card created successfully',
        ]);

    // Database assertions work the same way.
    $this->assertDatabaseHas('cards', [
        'deck_id' => $deck->id,
        'user_id' => $user->id,
        'question' => $cardData['question'],
        'answer' => $cardData['answer'],
    ]);
});

test('user cannot create card in other users deck', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user2->id]);

    $cardData = [
        'question' => 'What is the capital of France?',
        'answer' => 'Paris',
    ];

    actingAs($user1, 'sanctum')
        ->postJson("/api/v1/decks/{$deck->id}/cards", $cardData)
        ->assertForbidden();
});

test('card creation requires question and answer', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    actingAs($user, 'sanctum')
        ->postJson("/api/v1/decks/{$deck->id}/cards", [])
        ->assertUnprocessable() // Equivalent to assertStatus(422)
        ->assertJsonValidationErrors(['question', 'answer']);
});

// Using `describe` to group related tests for better organization.
describe('authentication', function () {
    test('decks api requires authentication', function () {
        getJson('/api/v1/decks')
            ->assertUnauthorized(); // Equivalent to assertStatus(401)
    });

    test('deck cards api requires authentication', function () {
        $deck = Deck::factory()->create();

        getJson("/api/v1/decks/{$deck->id}/cards")
            ->assertUnauthorized();
    });

    test('create card api requires authentication', function () {
        $deck = Deck::factory()->create();

        postJson("/api/v1/decks/{$deck->id}/cards", [
            'question' => 'Test question',
            'answer' => 'Test answer',
        ])->assertUnauthorized();
    });
});
