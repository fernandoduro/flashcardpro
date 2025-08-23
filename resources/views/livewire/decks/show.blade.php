<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $deck->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between">
                <a href="{{ route('study.show', $deck) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Study This Deck
                </a>
                 <!-- Add New Card Button Here -->
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Cards</h3>
                    <ul class="space-y-2">
                        @foreach($deck->cards as $card)
                            <li class="p-3 border rounded">
                                <p class="font-semibold">{{ $card->question }}</p>
                                <p class="text-gray-600">{{ $card->answer }}</p>
                                <button wire:click="$dispatch('openEditCardModal', { cardId: {{ $card->id }} })"
                                    class="text-gray-400 hover:text-indigo-600 group-hover:opacity-100 transition-opacity ml-4">
                                    Edit
                                </button>

                                <button wire:click="$dispatch('openConfirmationModal', {
                                    title: 'Remove Card',
                                            message: 'Are you sure you want to remove this card from the deck?',
                                            confirmAction: 'deleteCard',
                                            itemId: {{ $card->id }}
                                        })"
                                        class="text-gray-400 hover:text-red-600 group-hover:opacity-100 ...">
                                    Delete
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Add the Floating Action Button --}}
    <x-fab-link dispatch="openCreateCardModal" text="Add Card" deckId="{{ $deck->id }}" />

    <livewire:cards.form />
    <livewire:components.confirmation-modal />

</div>