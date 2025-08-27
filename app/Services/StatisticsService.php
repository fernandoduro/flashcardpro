<?php

namespace App\Services;

use App\Models\Study;
use App\Models\StudyResult;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get the total number of completed study sessions for a user.
     */
    public function getTotalCompletedStudies(int $userId): int
    {
        return Study::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->count();
    }

    /**
     * Get aggregate statistics about all answers for a user.
     */
    public function getAnswerStats(int $userId): object
    {
        return StudyResult::query()
            ->whereHas('study', fn ($query) => $query->where('user_id', $userId))
            ->selectRaw('
                count(*) as total_questions,
                avg(is_correct) * 100 as percentage_correct
            ')
            ->first() ?? (object) ['total_questions' => 0, 'percentage_correct' => 0];
    }

    /**
     * Find the card that the user has answered incorrectly most often.
     */
    public function getMostWrongedCard(int $userId): ?object
    {
        return StudyResult::query()
            ->join('studies', 'study_results.study_id', '=', 'studies.id')
            ->join('cards', 'study_results.card_id', '=', 'cards.id')
            ->where('studies.user_id', $userId)
            ->where('study_results.is_correct', false)
            ->select('cards.question', 'cards.answer', DB::raw('count(*) as incorrect_count'))
            ->groupBy('study_results.card_id', 'cards.question', 'cards.answer')
            ->orderByDesc('incorrect_count')
            ->first();
    }

    /**
     * Get the top 5 most studied decks for a user.
     */
    public function getDeckStudyRanking(int $userId): Collection
    {
        return Study::query()
            ->where('user_id', $userId)
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
    public function getStudiesPerDayChartData(int $userId): array
    {
        $studyCounts = Study::query()
            ->where('user_id', $userId)
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
     * Get comprehensive statistics for a user.
     */
    public function getUserStatistics(int $userId): array
    {
        return [
            'total_completed_studies' => $this->getTotalCompletedStudies($userId),
            'answer_stats' => $this->getAnswerStats($userId),
            'most_wronged_card' => $this->getMostWrongedCard($userId),
            'deck_study_ranking' => $this->getDeckStudyRanking($userId),
            'studies_per_day_chart' => $this->getStudiesPerDayChartData($userId),
        ];
    }
}
