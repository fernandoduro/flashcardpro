<?php

use App\Models\Deck;
use App\Models\Study;
use App\Models\User;
use App\Policies\StudyPolicy;

beforeEach(function () {
    $this->policy = new StudyPolicy;
});

test('user can view their own study', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $study = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    expect($this->policy->view($user, $study))->toBeTrue();
});

test('user cannot view other users study', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $otherUser->id]);
    $study = Study::factory()->create([
        'user_id' => $otherUser->id,
        'deck_id' => $deck->id,
    ]);

    expect($this->policy->view($user, $study))->toBeFalse();
});

test('user can create study for their own deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    expect($this->policy->create($user, $deck))->toBeTrue();
});

test('user cannot create study for other users deck', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $otherUser->id]);

    expect($this->policy->create($user, $deck))->toBeFalse();
});

test('user can update their own study', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $study = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    expect($this->policy->update($user, $study))->toBeTrue();
});

test('user cannot update other users study', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $otherUser->id]);
    $study = Study::factory()->create([
        'user_id' => $otherUser->id,
        'deck_id' => $deck->id,
    ]);

    expect($this->policy->update($user, $study))->toBeFalse();
});

test('user can delete their own study', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $study = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    expect($this->policy->delete($user, $study))->toBeTrue();
});

test('user cannot delete other users study', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $otherUser->id]);
    $study = Study::factory()->create([
        'user_id' => $otherUser->id,
        'deck_id' => $deck->id,
    ]);

    expect($this->policy->delete($user, $study))->toBeFalse();
});
