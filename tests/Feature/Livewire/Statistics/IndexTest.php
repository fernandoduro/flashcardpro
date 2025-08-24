<?php

use App\Livewire\Statistics\Index as StatisticsIndex;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Study;
use App\Models\StudyResult;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('component renders successfully', function () {
    Livewire::test(StatisticsIndex::class)->assertOk();
});

test('calculates total completed studies correctly', function () {
    $deck = Deck::factory()->for($this->user)->create();
    Study::factory()->for($this->user)->for($deck)->count(3)->create(['completed_at' => now()]);
    Study::factory()->for($this->user)->for($deck)->count(2)->create(['completed_at' => null]); // Incomplete

    Livewire::test(StatisticsIndex::class)
        ->assertSet('totalCompletedStudies', 3);
});

test('calculates answer statistics correctly', function () {
    $deck = Deck::factory()->for($this->user)->create();
    $study = Study::factory()->for($this->user)->for($deck)->create();
    $cards = Card::factory()->for($this->user)->count(4)->create();

    StudyResult::factory()->for($study)->for($cards[0])->create(['is_correct' => true]);
    StudyResult::factory()->for($study)->for($cards[1])->create(['is_correct' => true]);
    StudyResult::factory()->for($study)->for($cards[2])->create(['is_correct' => true]);
    StudyResult::factory()->for($study)->for($cards[3])->create(['is_correct' => false]);

    Livewire::test(StatisticsIndex::class)
        ->assertSet('answerStats.total_questions', 4)
        ->assertSet('answerStats.percentage_correct', 75.0);
});

test('finds the most wronged card', function () {
    $deck = Deck::factory()->for($this->user)->create();
    $study = Study::factory()->for($this->user)->for($deck)->create();
    $cardA = Card::factory()->for($this->user)->create(['question' => 'Card A Question']);
    $cardB = Card::factory()->for($this->user)->create();

    // Card A is wrong twice, Card B is wrong once
    StudyResult::factory()->for($study)->for($cardA)->create(['is_correct' => false]);
    StudyResult::factory()->for($study)->for($cardA)->create(['is_correct' => false]);
    StudyResult::factory()->for($study)->for($cardB)->create(['is_correct' => false]);
    StudyResult::factory()->for($study)->for($cardB)->create(['is_correct' => true]);

    Livewire::test(StatisticsIndex::class)
        ->assertSet('mostWrongedCard.question', 'Card A Question')
        ->assertSet('mostWrongedCard.incorrect_count', 2);
});