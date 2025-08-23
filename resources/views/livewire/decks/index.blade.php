<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Decks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Add New Deck Button Here -->

                    <ul class="space-y-4">
                        @forelse($decks as $deck)
                            <li class="p-4 border rounded-lg hover:bg-gray-50">
                                <a href="{{ route('decks.show', $deck) }}" class="block">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-bold">{{ $deck->name }}</h3>
                                        <span class="text-sm text-gray-500">{{ $deck->cards_count }} cards</span>
                                    </div>
                                </a>

                                <button wire:click="$dispatch('openEditModal', { deckId: {{ $deck->id }} })" class="text-gray-400 hover:text-indigo-600 group-hover:opacity-100 transition-opacity">
                                    Edit
                                </button>

                                <button wire:click="$dispatch('openConfirmationModal', {
                                        title: 'Delete Deck',
                                        message: 'Are you sure you want to delete this deck? All cards within it will be unassigned from this deck.',
                                        confirmAction: 'deleteDeck',
                                        itemId: {{ $deck->id }}
                                    })"
                                        class="text-gray-400 hover:text-red-600 group-hover:opacity-100 transition-opacity">
                                    Delete
                                </button>
                            </li>
                        @empty
                            <p>You haven't created any decks yet.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Add the Floating Action Button --}}
    <x-fab-link dispatch="openCreateModal" text="Add Deck" />

    <livewire:decks.form/>
    <livewire:components.confirmation-modal />
</div>

