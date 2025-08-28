<?php

use App\Models\Card;
use App\Models\Deck;
use App\Models\Study;
use App\Models\StudyResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('study result belongs to study', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();
    $studyResult = StudyResult::factory()->for($study)->for($card)->create();

    expect($studyResult->study)->toBeInstanceOf(Study::class);
    expect($studyResult->study->id)->toBe($study->id);
});

test('study result belongs to card', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();
    $studyResult = StudyResult::factory()->for($study)->for($card)->create();

    expect($studyResult->card)->toBeInstanceOf(Card::class);
    expect($studyResult->card->id)->toBe($card->id);
});

test('study result belongs to user through study', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();
    $studyResult = StudyResult::factory()->for($study)->for($card)->create();

    expect($studyResult->user)->toBeInstanceOf(User::class);
    expect($studyResult->user->id)->toBe($user->id);
});

test('study result belongs to deck through study', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();
    $studyResult = StudyResult::factory()->for($study)->for($card)->create();

    expect($studyResult->deck)->toBeInstanceOf(Deck::class);
    expect($studyResult->deck->id)->toBe($deck->id);
});

test('study result has fillable attributes', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    $studyResultData = [
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => true,
    ];

    $studyResult = StudyResult::create($studyResultData);

    expect($studyResult->study_id)->toBe($study->id);
    expect($studyResult->card_id)->toBe($card->id);
    expect($studyResult->is_correct)->toBeTrue();
});

test('study result is_correct is required', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    try {
        $studyResult = new StudyResult([
            'study_id' => $study->id,
            'card_id' => $card->id,
        ]);
        $studyResult->save();
        expect(false)->toBeTrue('Should have thrown an exception for missing is_correct');
    } catch (\Exception $e) {
        expect($e)->toBeInstanceOf(\Exception::class);
    }
});

test('study result is_correct can be true or false', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    $correctResult = StudyResult::create([
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => true,
    ]);

    $incorrectResult = StudyResult::create([
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => false,
    ]);

    expect($correctResult->is_correct)->toBeTrue();
    expect($incorrectResult->is_correct)->toBeFalse();
});

test('study result has timestamps', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();
    $studyResult = StudyResult::factory()->for($study)->for($card)->create();

    expect($studyResult->created_at)->not->toBeNull();
    expect($studyResult->updated_at)->not->toBeNull();
    expect($studyResult->created_at)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($studyResult->updated_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('study result can be deleted', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();
    $studyResult = StudyResult::factory()->for($study)->for($card)->create();

    $studyResult->delete();

    expect(StudyResult::find($studyResult->id))->toBeNull();
});

test('study result can be updated', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();
    $studyResult = StudyResult::factory()->for($study)->for($card)->create([
        'is_correct' => false,
    ]);

    $studyResult->update(['is_correct' => true]);

    expect($studyResult->fresh()->is_correct)->toBeTrue();
});

test('study result can query by correctness', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card1 = Card::factory()->for($user)->for($deck)->create();
    $card2 = Card::factory()->for($user)->for($deck)->create();

    StudyResult::factory()->for($study)->for($card1)->create(['is_correct' => true]);
    StudyResult::factory()->for($study)->for($card2)->create(['is_correct' => false]);

    $correctResults = StudyResult::where('is_correct', true)->get();
    $incorrectResults = StudyResult::where('is_correct', false)->get();

    expect($correctResults)->toHaveCount(1);
    expect($incorrectResults)->toHaveCount(1);
});

test('study result allows multiple results for same study and card', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    StudyResult::create([
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => true,
    ]);

    // This should succeed since there's no unique constraint
    $result2 = StudyResult::create([
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => false,
    ]);

    expect($result2)->not->toBeNull();
    expect(StudyResult::where('study_id', $study->id)->where('card_id', $card->id)->count())->toBe(2);
});
