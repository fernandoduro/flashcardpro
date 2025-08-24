<div>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-4">
            @if ($deck->cover_image_path)
                <img src="{{ asset('storage/' . $deck->cover_image_path) }}" alt="{{ $deck->name }}"
                     class="h-20 w-20 object-cover object-center transition-transform group-hover:scale-105">
            @else
                <div class="h-20 w-20 flex items-center justify-center bg-gray-700 rounded-md">
                     <i class="fa-solid fa-layer-group text-5xl text-gray-500"></i>
                </div>
            @endif
            {{ $deck->name }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center">
                <div class="mb-4 flex justify-between  gap-4">
                    <a href="{{ route('study.show', $deck) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:border-primary-700 focus:ring ring-primary-300 disabled:opacity-25 transition ease-in-out duration-150">
                    &#9658; Study This Deck
                    </a>

                    <x-fab-link dispatch="openCreateCardModal" text="Add Card" deckId="{{ $deck->id }}" />
                </div>
            </div>

            <div>
                <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($deck->cards as $card)
                        <li wire:key="{{ $card->id }}"
                            class="group relative col-span-1 bg-gray-100 rounded-lg shadow-lg p-6
                                transition-transform duration-300 ease-in-out
                                hover:scale-105 hover:shadow-xl hover:z-10
                                odd:rotate-1 even:-rotate-1">

                            {{-- Action Buttons (Top Right Corner) --}}
                            <div class="absolute top-2 right-2 flex items-center space-x-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                <button wire:click="$dispatch('openEditCardModal', { cardId: {{ $card->id }} })"
                                        class="h-8 w-8 flex items-center justify-center rounded-full bg-black/10 text-gray-700 hover:bg-black/20"
                                        title="Edit Card">
                                    <i class="fa-solid fa-fw fa-pen-to-square"></i>
                                </button>
                                <button wire:click="$dispatch('openConfirmationModal', { title: 'Remove Card', message: 'Are you sure?', confirmAction: 'deleteCard', itemId: {{ $card->id }} })"
                                        class="h-8 w-8 flex items-center justify-center rounded-full bg-black/10 text-gray-700 hover:bg-black/20"
                                        title="Remove Card">
                                    <i class="fa-solid fa-fw fa-trash-can"></i>
                                </button>
                            </div>

                            {{-- Card Content --}}
                            <div class="flex flex-col h-full">
                                <p class="font-semibold text-gray-800">{{ $card->question }}</p>
                                
                                <p class="text-gray-600 flex-grow">{{ $card->answer }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="col-span-full">
                            <div class="text-center bg-white rounded-lg shadow p-8">
                                <h3 class="text-lg font-medium text-gray-900">No Cards Yet!</h3>
                                <p class="mt-1 text-sm text-gray-500">This deck is empty. Click the "Add Card" button to get started.</p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Add the Floating Action Button --}}
    <livewire:cards.form />
    <livewire:components.confirmation-modal />

</div>