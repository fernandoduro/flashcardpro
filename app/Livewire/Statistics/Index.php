<?php

namespace App\Livewire\Statistics;

use App\Models\Card;
use App\Models\Study;
use App\Models\StudyResult;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $totalCompletedStudies = 0;
    public $totalQuestionsAnswered = 0;
    public $percentageCorrect = 0;
    public $mostWrongedCard = null;
    public $deckStudyRanking = [];
    public array $studiesPerDayChartData = [];
    

    public function mount()
    {
        $userId = auth()->id();

        // --- Prepare main stats ---
        $this->totalCompletedStudies = Study::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->count();

        $this->totalQuestionsAnswered = StudyResult::whereHas('study', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $this->percentageCorrect = StudyResult::whereHas('study', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->select(DB::raw('AVG(CASE WHEN is_correct THEN 1 ELSE 0 END) * 100 as percentage_correct'))
          ->value('percentage_correct') ?? 0;

        // 2. Find and set the most wronged question
        $mostWronged = StudyResult::query()
            ->join('studies', 'study_results.study_id', '=', 'studies.id')
            ->where('studies.user_id', $userId)
            ->where('is_correct', false)
            ->select('card_id', DB::raw('count(*) as incorrect_count'))
            ->groupBy('card_id')
            ->orderByDesc('incorrect_count')
            ->first();

        if ($mostWronged) {
            $card = Card::find($mostWronged->card_id);
            $this->mostWrongedCard = (object) [
                'question' => $card?->question,
                'answer' => $card?->answer,
                'count' => $mostWronged->incorrect_count,
            ];
        } else {
            $this->mostWrongedCard = null; // Ensure it's null if no data
        }

        // 3. Get and set ranking of studies per deck
        $this->deckStudyRanking = Study::query()
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->select('deck_id', DB::raw('count(*) as study_count'))
            ->with('deck:id,name')
            ->groupBy('deck_id')
            ->orderByDesc('study_count')
            ->limit(5)
            ->get();

        $this->studiesPerDayChartData = $this->prepareStudiesPerDayChartData($userId);
    }

    protected function prepareStudiesPerDayChartData(int $userId): array
    {
        $studyCounts = Study::query()
            ->where('user_id', $userId)
            ->where('completed_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $period = CarbonPeriod::create(now()->subDays(29), now());
        $dates = [];
        $counts = [];

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->format('M j');
            $counts[] = $studyCounts[$formattedDate]->count ?? 0;
        }

        return [
            'categories' => $dates,
            'data' => $counts,
        ];
    }

    public function render()
    {
        // Now the render method is very clean.
        return view('livewire.statistics.index')
            ->layout('layouts.app', ['title' => 'My Statistics']);
    }
}