<?php

use App\Http\Requests\Api\V1\StoreStudyResultRequest;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Study;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

test('store study result request has correct validation rules', function () {
    $user = User::factory()->create();

    $request = new StoreStudyResultRequest();
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    // Note: Rules depend on user context, so we'll test the structure
    $rules = $request->rules();

    expect($rules)->toHaveKey('study_id');
    expect($rules)->toHaveKey('card_id');
    expect($rules)->toHaveKey('is_correct');

    expect($rules['study_id'])->toContain('required');
    expect($rules['study_id'])->toContain('integer');
    expect($rules['card_id'])->toContain('required');
    expect($rules['card_id'])->toContain('integer');
    expect($rules['is_correct'])->toContain('required');
    expect($rules['is_correct'])->toContain('boolean');
});

test('store study result request authorization with own study', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $study = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id
    ]);

    $request = new StoreStudyResultRequest();
    $request->merge(['study_id' => $study->id]);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    expect($request->authorize())->toBeTrue();
});

test('store study result request authorization with other users study', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $study = Study::factory()->create(['user_id' => $otherUser->id]);

    $request = new StoreStudyResultRequest();
    $request->merge(['study_id' => $study->id]);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    expect($request->authorize())->toBeFalse();
});

test('store study result request authorization with nonexistent study', function () {
    $user = User::factory()->create();

    $request = new StoreStudyResultRequest();
    $request->merge(['study_id' => 99999]); // Non-existent ID
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    expect($request->authorize())->toBeFalse();
});

test('store study result request validation passes with valid data', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $study = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id
    ]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id
    ]);

    $data = [
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => true
    ];

    $request = new StoreStudyResultRequest();
    $request->merge(['user' => $user]);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $validator = validator($data, $request->rules());

    expect($validator->fails())->toBeFalse();
});

test('store study result request validation fails with missing study id', function () {
    $user = User::factory()->create();

    $data = [
        'card_id' => 1,
        'is_correct' => true
    ];

    $request = new StoreStudyResultRequest();
    $request->merge(['user' => $user]);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $validator = validator($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('study_id');
});

test('store study result request validation fails with missing card id', function () {
    $user = User::factory()->create();

    $data = [
        'study_id' => 1,
        'is_correct' => true
    ];

    $request = new StoreStudyResultRequest();
    $request->merge(['user' => $user]);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $validator = validator($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('card_id');
});

test('store study result request validation fails with missing is correct', function () {
    $user = User::factory()->create();

    $data = [
        'study_id' => 1,
        'card_id' => 1
    ];

    $request = new StoreStudyResultRequest();
    $request->merge(['user' => $user]);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $validator = validator($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('is_correct');
});

test('store study result request validation fails with invalid is correct', function () {
    $user = User::factory()->create();

    $data = [
        'study_id' => 1,
        'card_id' => 1,
        'is_correct' => 'not_boolean'
    ];

    $request = new StoreStudyResultRequest();
    $request->merge(['user' => $user]);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $validator = validator($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('is_correct');
});
