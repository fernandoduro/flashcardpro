<?php

use App\Http\Requests\Api\V1\StoreStudyRequest;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rule;

uses(RefreshDatabase::class);

test('store study request has correct validation rules', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $request = new StoreStudyRequest();
    $request->setUserResolver(fn() => $user);
    $rules = $request->rules();

    expect($rules)->toHaveKey('deck_id');
    expect($rules['deck_id'])->toBeArray();
    expect($rules['deck_id'])->toContain('required');
    expect($rules['deck_id'])->toContain('integer');

    // Check that the exists rule is present (can't compare objects directly)
    $existsRule = null;
    foreach ($rules['deck_id'] as $rule) {
        if ($rule instanceof \Illuminate\Validation\Rules\Exists) {
            $existsRule = $rule;
            break;
        }
    }
    expect($existsRule)->not->toBeNull();
});

test('store study request passes validation with valid data', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => $deck->id]);

    // Mock user context for validation
    $request->setUserResolver(fn() => $user);

    $validator = validator($request->all(), $request->rules());
    expect($validator->passes())->toBeTrue();
});

test('store study request fails validation with missing deck_id', function () {
    $user = User::factory()->create();

    $request = new StoreStudyRequest();
    $request->merge([]);

    // Mock user context for validation
    $request->setUserResolver(fn() => $user);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('deck_id'))->toBeTrue();
});

test('store study request fails validation with non-existent deck', function () {
    $user = User::factory()->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => 999]);

    // Mock user context for validation
    $request->setUserResolver(fn() => $user);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('deck_id'))->toBeTrue();
});

test('store study request fails validation with another users deck', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->for($user1)->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => $deck->id]);

    // Mock user2 context for validation
    $request->setUserResolver(fn() => $user2);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('deck_id'))->toBeTrue();
});

test('store study request fails validation with invalid deck_id type', function () {
    $user = User::factory()->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => 'invalid']);

    // Mock user context for validation
    $request->setUserResolver(fn() => $user);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('deck_id'))->toBeTrue();
});

test('store study request fails validation with zero deck_id', function () {
    $user = User::factory()->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => 0]);

    // Mock user context for validation
    $request->setUserResolver(fn() => $user);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('deck_id'))->toBeTrue();
});

test('store study request fails validation with negative deck_id', function () {
    $user = User::factory()->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => -1]);

    // Mock user context for validation
    $request->setUserResolver(fn() => $user);

    $validator = validator($request->all(), $request->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('deck_id'))->toBeTrue();
});

test('store study request authorization passes for own deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => $deck->id]);
    $request->setUserResolver(fn() => $user);

    expect($request->authorize())->toBeTrue();
});

test('store study request authorization fails for another users deck', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->for($user1)->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => $deck->id]);
    $request->setUserResolver(fn() => $user2);

    expect($request->authorize())->toBeFalse();
});

test('store study request authorization fails without user', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => $deck->id]);
    $request->setUserResolver(fn() => null);

    expect($request->authorize())->toBeFalse();
});

test('store study request authorization fails with non-existent deck', function () {
    $user = User::factory()->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => 999]);
    $request->setUserResolver(fn() => $user);

    expect($request->authorize())->toBeFalse();
});

test('store study request always returns true for authorize when deck exists and belongs to user', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    $request = new StoreStudyRequest();
    $request->merge(['deck_id' => $deck->id]);
    $request->setUserResolver(fn() => $user);

    // Call authorize multiple times to ensure consistency
    expect($request->authorize())->toBeTrue();
    expect($request->authorize())->toBeTrue();
});
