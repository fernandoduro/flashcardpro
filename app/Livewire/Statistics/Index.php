<?php

namespace App\Livewire\Statistics;

use App\Models\Study;
use App\Models\StudyResult;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    /**
     * Get the total number of completed study sessions for the user.
     */
    #[Computed]
    public function totalCompletedStudies(): int
    {
        return Study::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->count();
    }

    /**
     * Get aggregate statistics about all answers.
     * This combines three queries into one for efficiency.
     */
    #[Computed]
    public function answerStats(): object
    {
        return StudyResult::query()
            ->whereHas('study', fn (Builder $query) => $query->where('user_id', auth()->id()))
            ->selectRaw('
                count(*) as total_questions,
                avg(is_correct) * 100 as percentage_correct
            ')
            ->first() ?? (object) ['total_questions' => 0, 'percentage_correct' => 0];
    }

    /**
     * Find the card that the user has answered incorrectly most often.
     */
    #[Computed]
    public function mostWrongedCard(): ?object
    {
        return StudyResult::query()
            ->join('studies', 'study_results.study_id', '=', 'studies.id')
            ->join('cards', 'study_results.card_id', '=', 'cards.id')
            ->where('studies.user_id', auth()->id())
            ->where('study_results.is_correct', false)
            ->select('cards.question', 'cards.answer', DB::raw('count(*) as incorrect_count'))
            ->groupBy('study_results.card_id', 'cards.question', 'cards.answer')
            ->orderByDesc('incorrect_count')
            ->first();
    }

    /**
     * Get the top 5 most studied decks.
     */
    #[Computed]
    public function deckStudyRanking(): Collection
    {
        return Study::query()
            ->where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->select('deck_id', DB::raw('count(*) as study_count'))
            ->with('deck:id,name')
            ->groupBy('deck_id')
            ->orderByDesc('study_count')
            ->limit(5)
            ->get();
    }

    /**
     * Prepare data for the studies per day line chart.
     */
    #[Computed]
    public function studiesPerDayChartData(): array
    {
        $studyCounts = Study::query()
            ->where('user_id', auth()->id())
            ->where('completed_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->keyBy(fn ($item) => $item->date);

        $period = CarbonPeriod::create(now()->subDays(29), now());
        $dates = [];
        $counts = [];

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->format('M j');
            $counts[] = $studyCounts->get($formattedDate)?->count ?? 0;
        }

        return [
            'categories' => $dates,
            'data' => $counts,
        ];
    }

    /**
     * Render the component.
     *
     * Computed properties are automatically passed to the view,
     * so we don't need to pass any data manually.
     */
    public function render(): View
    {
        return view('livewire.statistics.index')
            ->layout('layouts.app', ['title' => 'Statistics']);
    }
}