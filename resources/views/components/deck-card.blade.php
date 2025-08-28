@props(['deck'])

{{-- The root element. Using a div is semantically better than a li here as it's a self-contained component --}}
<div class="group relative col-span-1 bg-white rounded-lg shadow-md transition hover:shadow-xl hover:-translate-y-1">

    {{-- Action Buttons Dropdown (Cleaner UX) --}}
    <div class="absolute top-2 right-2 z-10">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="cursor-pointer h-8 w-8 flex items-center justify-center rounded-full bg-white/70 backdrop-blur-sm text-gray-600 hover:bg-gray-200">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link href="#" wire:click.prevent="togglePin({{ $deck->id }})">
                    <i class="fa-solid fa-fw fa-thumbtack mr-2 {{ $deck->is_pinned ? 'text-primary-500' : 'text-gray-400' }}"></i>
                    {{ $deck->is_pinned ? 'Unpin' : 'Pin' }}
                </x-dropdown-link>
                <x-dropdown-link href="#" wire:click.prevent="$dispatch('openEditModal', { deckId: {{ $deck->id }} })">
                    <i class="fa-solid fa-fw fa-pen-to-square mr-2 text-gray-400"></i>
                    Edit
                </x-dropdown-link>
                <x-dropdown-link href="#" wire:click.prevent="$dispatch('openConfirmationModal', { title: 'Delete Deck', message: 'Are you sure you want to permanently delete this deck?', confirmAction: 'deleteDeck', itemId: {{ $deck->id }} })">
                    <i class="fa-solid fa-fw fa-trash-can mr-2 text-gray-400"></i>
                    Delete
                </x-dropdown-link>
            </x-slot>
        </x-dropdown>
    </div>

    {{-- Main Clickable Area --}}
    <a href="{{ route('decks.show', $deck) }}" wire:navigate>
        {{-- Cover Image --}}
        <div class="aspect-video w-full overflow-hidden rounded-t-lg">
            @if ($deck->cover_image_path)
                <img src="{{ asset('storage/' . $deck->cover_image_path) }}" alt="{{ $deck->name }}"
                     class="h-full w-full object-cover object-center transition-transform group-hover:scale-105">
            @else
                <div class="h-full w-full flex items-center justify-center bg-gray-200 rounded-t-lg">
                     <i class="fa-solid fa-layer-group text-5xl text-gray-400"></i>
                </div>
            @endif
        </div>
        {{-- Details Section --}}
        <div class="p-4">
            <h3 class="font-bold text-gray-800 truncate">{{ $deck->name }}</h3>
            <p class="text-sm text-gray-500">{{ $deck->cards_count }} cards</p>
        </div>
    </a>
</div>