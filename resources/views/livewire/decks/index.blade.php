<div>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Action Bar --}}
            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-700">All Decks</h3>
                <x-fab-link dispatch="openCreateModal" text="Add Deck" />
            </div>

            {{-- Responsive Card Grid --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @forelse ($this->decks as $deck)
                    {{-- The deck-card component is called here. Note the wire:key for performance. --}}
                    <x-deck-card :deck="$deck" wire:key="deck-{{ $deck->id }}" />
                @empty
                    {{-- A professional "Empty State" view --}}
                    <div class="col-span-full mt-8">
                        <div class="text-center">
                            <div class="mx-auto h-24 w-24 text-primary-400 mb-4">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="mt-2 text-xl font-semibold text-gray-900">Ready to start learning?</h3>
                            <p class="mt-1 text-sm text-gray-500 max-w-md mx-auto">
                                Create your first deck and begin your journey to mastering new concepts.
                                Whether it's for school, work, or personal growth, flashcards are your perfect study companion.
                            </p>
                            <div class="mt-6">
                                <button type="button" wire:click="$dispatch('openCreateModal')" class="inline-flex items-center rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    <i class="fa-solid fa-plus -ml-0.5 mr-1.5 h-5 w-5"></i>
                                    Create Your First Deck
                                </button>
                            </div>
                            <div class="mt-4 text-xs text-gray-400">
                                <p>💡 Tip: Try our AI card generator to create decks automatically!</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <livewire:decks.form/>
    <livewire:components.confirmation-modal />
</div>