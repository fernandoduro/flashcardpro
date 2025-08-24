@props(['deck'])

<div class="group relative bg-primary-800 hover:bg-primary-700 rounded-lg p-4 transition-colors duration-200">

    {{-- Action Buttons (Now includes Pin) --}}
    <div class="absolute top-2 right-2 flex flex-col items-center space-y-2 opacity-80 group-hover:opacity-100 transition-opacity duration-300 z-10">
        <button wire:click.prevent="togglePin({{ $deck->id }})" title="Pin Deck"
                class="h-9 w-9 flex items-center justify-center rounded-full bg-black/50 backdrop-blur-sm text-white hover:bg-black/70">
            <i class="fa-solid fa-thumbtack {{ $deck->is_pinned ? 'text-green-400' : '' }}"></i>
        </button>
        <button wire:click.prevent="$dispatch('openEditModal', { deckId: {{ $deck->id }} })" title="Edit Deck"
                class="h-9 w-9 flex items-center justify-center rounded-full bg-black/50 backdrop-blur-sm text-white hover:bg-black/70">
            <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button wire:click="$dispatch('openConfirmationModal', {
            title: 'Delete Deck',
            message: 'Are you sure you want to delete this deck?',
            confirmAction: 'deleteDeck',
            itemId: {{ $deck->id }}
        })" title="Delete Deck"
                class="h-9 w-9 flex items-center justify-center rounded-full bg-black/50 backdrop-blur-sm text-white hover:bg-black/70">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    </div>

    {{-- Card Content --}}
    <a href="{{ route('decks.show', $deck) }}" wire:navigate>
        <div class="aspect-square w-full overflow-hidden rounded-md mb-4">
            @if ($deck->cover_image_path)
                <img src="{{ asset('storage/' . $deck->cover_image_path) }}" alt="{{ $deck->name }}"
                     class="h-full w-full object-cover object-center transition-transform group-hover:scale-105">
            @else
                <div class="h-full w-full flex items-center justify-center bg-gray-700 rounded-md">
                     <i class="fa-solid fa-layer-group text-5xl text-gray-500"></i>
                </div>
            @endif
        </div>
        <h3 class="font-bold text-white truncate">{{ $deck->name }}</h3>
        <p class="text-sm text-gray-400">{{ $deck->cards_count }} cards</p>
    </a>
</div>