<?php

namespace App\Livewire\Statistics;

use App\Services\StatisticsService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    protected StatisticsService $statisticsService;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->statisticsService = new StatisticsService;
    }

    /**
     * Get the total number of completed study sessions for the user.
     */
    #[Computed(cache: true, seconds: 60)]
    public function totalCompletedStudies(): int
    {
        return $this->statisticsService->getTotalCompletedStudies(auth()->id());
    }

    /**
     * Get aggregate statistics about all answers.
     * This combines three queries into one for efficiency.
     */
    #[Computed(cache: true, seconds: 60)]
    public function answerStats(): object
    {
        return $this->statisticsService->getAnswerStats(auth()->id());
    }

    /**
     * Find the card that the user has answered incorrectly most often.
     */
    #[Computed(cache: true, seconds: 60)]
    public function mostWrongedCard(): ?object
    {
        return $this->statisticsService->getMostWrongedCard(auth()->id());
    }

    /**
     * Get the top 5 most studied decks.
     */
    #[Computed(cache: true, seconds: 60)]
    public function deckStudyRanking(): \Illuminate\Support\Collection
    {
        return $this->statisticsService->getDeckStudyRanking(auth()->id());
    }

    /**
     * Prepare data for the studies per day line chart.
     */
    #[Computed(cache: true, seconds: 60)]
    public function studiesPerDayChartData(): array
    {
        return $this->statisticsService->getStudiesPerDayChartData(auth()->id());
    }

    /**
     * Render the component.
     *
     * Computed properties are automatically passed to the view,
     * so we don't need to pass any data manually.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.statistics.index')
            ->layout('layouts.app', ['title' => 'Statistics']);
    }
}
