<div>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Stat Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Total Studies Card --}}
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col">
                    <h3 class="text-lg font-medium text-gray-500">Total Study Sessions</h3>
                    <p class="mt-2 text-5xl font-bold text-primary-600">{{ $this->totalCompletedStudies }}</p>
                    <div class="mt-auto pt-4 flex justify-between text-sm text-gray-500">
                        <span>Questions Answered: <span class="font-semibold text-gray-700">{{ $this->answerStats->total_questions }}</span></span>
                        <span>Success Rate: <span class="font-semibold text-gray-700">{{ round($this->answerStats->percentage_correct) }}%</span></span>
                    </div>
                </div>

                {{-- Most Wronged Question Card --}}
                <div class="bg-white p-6 rounded-lg shadow-md md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-500">Most Challenging Card</h3>
                    @if ($this->mostWrongedCard)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">You answered this incorrectly <span class="font-bold text-red-600">{{ $this->mostWrongedCard->incorrect_count }}</span> time(s).</p>
                            <div class="mt-4 p-4 border-l-4 border-red-300 bg-red-50 rounded">
                                <p class="font-semibold text-gray-800">Q: {{ $this->mostWrongedCard->question }}</p>
                                <p class="mt-2 text-gray-600">A: {{ $this->mostWrongedCard->answer }}</p>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-gray-600">No incorrect answers recorded yet. Great job!</p>
                    @endif
                </div>

                {{-- Deck Ranking Card --}}
                <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-3">
                    <h3 class="text-lg font-medium text-gray-500">Most Studied Decks (Top 5)</h3>
                    @forelse ($this->deckStudyRanking as $rank)
                        <div class="flex items-center justify-between py-3 border-b last:border-b-0">
                            <span class="text-gray-700 font-medium">{{ $rank->deck->name }}</span>
                            <span class="font-bold text-primary-600">{{ $rank->study_count }} sessions</span>
                        </div>
                    @empty
                        <p class="mt-4 text-gray-600">You haven't completed any study sessions yet.</p>
                    @endforelse
                </div>

                {{-- Line Chart Card with Scoped Alpine Logic --}}
                <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-3"
                     x-data="{
                         initChart() {
                             const chartData = @js($this->studiesPerDayChartData);
                             Highcharts.chart('studies-chart-container', {
                                 chart: { type: 'line', height: 300 },
                                 title: { text: '' },
                                 xAxis: { categories: chartData.categories },
                                 yAxis: { title: { text: 'Number of Sessions' }, allowDecimals: false, min: 0 },
                                 legend: { enabled: false },
                                 series: [{ name: 'Study Sessions', data: chartData.data, color: '#4f46e5' }],
                                 credits: { enabled: false },
                                 tooltip: { pointFormat: '<b>{point.y}</b> session(s) completed' }
                             });
                         }
                     }"
                     x-init="initChart()">
                    <h3 class="text-lg font-medium text-gray-500">Study Sessions (Last 30 Days)</h3>
                    <div id="studies-chart-container" wire:ignore class="mt-4"></div>
                </div>

            </div>
        </div>
    </div>
</div>