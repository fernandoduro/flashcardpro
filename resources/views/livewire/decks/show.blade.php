<div>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-4">
            @if ($deck->cover_image_path)
                <img src="{{ asset('storage/' . $deck->cover_image_path) }}" alt="{{ $deck->name }}"
                     class="h-12 w-12 object-cover rounded-md">
            @else
                <div class="h-12 w-12 flex items-center justify-center bg-gray-200 rounded-md">
                     <i class="fa-solid fa-layer-group text-3xl text-gray-400"></i>
                </div>
            @endif
            <span>{{ $deck->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Main Action Bar --}}
            <div class="mb-8 flex justify-between items-center">
                <button wire:click="openStudyModal" class="inline-flex items-center gap-x-2 px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 transition-colors">
                    <i class="fa-solid fa-play"></i>
                    Study This Deck
                </button>
                <div class="flex items-center space-x-2">
                    <button wire:click="generateAiCards" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-x-2 px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-500 transition-colors disabled:opacity-75">
                        <span wire:loading.remove wire:target="generateAiCards">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            Generate AI Cards
                        </span>
                        <span wire:loading wire:target="generateAiCards">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            Generating...
                        </span>
                    </button>
                    <x-fab-link dispatch="openCreateCardModal" text="Add Card" deckId="{{ $deck->id }}" />
                </div>
            </div>

            {{-- Post-it Note Card Grid --}}
            <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse($deck->cards as $card)
                    <li wire:key="card-{{ $card->id }}"
                        class="group relative col-span-1 bg-gray-200 rounded-lg shadow-lg p-6 flex flex-col
                               transition-transform duration-300 ease-in-out hover:scale-105 hover:shadow-xl hover:z-10
                               odd:rotate-1 even:-rotate-1">

                        {{-- Action Buttons --}}
                        <div class="absolute top-2 right-2 flex items-center space-x-1">
                            <button wire:click.prevent="$dispatch('openEditCardModal', { cardId: {{ $card->id }} })"
                                    class="h-8 w-8 flex items-center justify-center rounded-full bg-black/10 text-gray-700 hover:bg-black/20"
                                    title="Edit Card">
                                <i class="fa-solid fa-fw fa-pen-to-square"></i>
                            </button>
                            <button wire:click.prevent="$dispatch('openConfirmationModal', {
                                        title: 'Delete Card',
                                        message: 'Are you sure you want to permanently delete this card?',
                                        confirmAction: 'deleteCard',
                                        itemId: {{ $card->id }}
                                    })"
                                    class="h-8 w-8 flex items-center justify-center rounded-full bg-black/10 text-gray-700 hover:bg-black/20"
                                    title="Delete Card">
                                <i class="fa-solid fa-fw fa-trash-can"></i>
                            </button>
                        </div>

                        {{-- Card Content --}}
                        <div class="flex flex-col h-full space-y-2 mt-6">
                            <p class="font-semibold text-gray-800">{{ $card->question }}</p>
                            <hr class="border-gray-300/60">
                            <p class="text-gray-600 flex-grow">{{ $card->answer }}</p>
                        </div>
                    </li>
                @empty
                    <li class="col-span-full">
                        <div class="text-center bg-white rounded-lg shadow p-8">
                             <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No cards yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding a card to this deck.</p>
                            <div class="mt-6">
                               <button type="button" wire:click="$dispatch('openCreateCardModal', { deckId: {{ $deck->id }} })" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                                    <i class="fa-solid fa-plus -ml-0.5 mr-1.5 h-5 w-5"></i>
                                    Add First Card
                                </button>
                            </div>
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Modals --}}
    <livewire:cards.form />
    <livewire:components.confirmation-modal />
    @include('livewire.decks.study-config-modal')
</div>