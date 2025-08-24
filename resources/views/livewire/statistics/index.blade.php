<div
    {{-- 1. Initialize an Alpine component --}}
    x-data="{
        initChart(chartData) {
            // This function is now just a plain JS function
            // It will be called by x-init
            Highcharts.chart('studies-chart-container', {
                chart: { type: 'line', height: 300 },
                title: { text: '' },
                xAxis: {
                    categories: chartData.categories,
                    labels: { style: { color: '#6b7280' } }
                },
                yAxis: {
                    title: { text: 'Number of Sessions', style: { color: '#6b7280' } },
                    allowDecimals: false,
                    min: 0,
                },
                legend: { enabled: false },
                series: [{
                    name: 'Study Sessions',
                    data: chartData.data,
                    color: '#4f46e5'
                }],
                credits: { enabled: false },
                tooltip: { pointFormat: '<b>{point.y}</b> session(s) completed' }
            });
        }
    }"
    {{-- 2. Use x-init to call the function when the component loads --}}
    {{--    The @js directive safely passes the public property to JS --}}
    x-init="initChart(@js($studiesPerDayChartData))"
>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Stat Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- 1. Total Studies Card --}}
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-500">Total Study Sessions</h3>
                    <p class="mt-2 text-5xl font-bold text-primary-600">{{ $totalCompletedStudies }}</p>
                    <p class="mt-1 text-sm text-gray-500">Completed sessions</p>
                    <p class="mt-2 text-5xl font-bold text-primary-600">{{ $totalQuestionsAnswered }}</p>
                    <p class="mt-1 text-sm text-gray-500">Questons Answered</p>
                    <p class="mt-2 text-5xl font-bold text-primary-600">{{ ceil($percentageCorrect) }}%</p>
                    <p class="mt-1 text-sm text-gray-500">Win Rate</p>
                </div>

                {{-- 2. Most Wronged Question Card --}}
                <div class="bg-white p-6 rounded-lg shadow-md md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-500">Most Challenging Card</h3>
                    @if ($mostWrongedCard)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">You answered this card incorrectly <span class="font-bold text-red-600">{{ $mostWrongedCard->count }}</span> time(s).</p>
                            <div class="mt-4 p-4 border-l-4 border-red-300 bg-red-50 rounded">
                                <p class="font-semibold text-gray-800">Q: {{ $mostWrongedCard->question }}</p>
                                <p class="mt-2 text-gray-600">A: {{ $mostWrongedCard->answer }}</p>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-gray-600">No incorrect answers recorded yet. Great job!</p>
                    @endif
                </div>

                {{-- 3. Deck Ranking Card --}}
                <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-3">
                    <h3 class="text-lg font-medium text-gray-500">Most Studied Decks (Top 5)</h3>
                    @forelse ($deckStudyRanking as $rank)
                        <div class="mt-4 flex items-center justify-between py-2 border-b last:border-b-0">
                            <span class="text-gray-700 font-medium">{{ $rank->deck->name }}</span>
                            <span class="font-bold text-primary-600">{{ $rank->study_count }} sessions</span>
                        </div>
                    @empty
                        <p class="mt-2 text-gray-600">You haven't completed any study sessions yet.</p>
                    @endforelse
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-3">
                    <h3 class="text-lg font-medium text-gray-500">Study Sessions (Last 30 Days)</h3>
                    {{-- 3. Add wire:ignore to the container to prevent Livewire from re-rendering it --}}
                    <div id="studies-chart-container" wire:ignore class="mt-4"></div>
                </div>

            </div>
        </div>
    </div>
</div>