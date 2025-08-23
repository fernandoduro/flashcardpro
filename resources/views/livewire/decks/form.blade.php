<div
    x-data="{ show: false }"
    x-on:open-deck-modal.window="show = true"
    x-on:close-deck-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50"
    style="display: none;"
>
    {{-- Modal backdrop --}}
    <div x-show="show" @mousedown="show = false" x-transition.opacity class="fixed inset-0 bg-gray-800 bg-opacity-75"></div>

    {{-- Modal content --}}
    <div x-show="show" x-transition
         @mousedown.stop
         class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-auto my-8 relative">

        <div class="flex justify-between items-center pb-3 border-b">
            {{-- DYNAMIC TITLE --}}
            <h3 class="text-2xl font-bold">
                @if (!empty($editingDeck)) Edit Deck @else Create New Deck @endif
            </h3>
            <button @click="show = false" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

                {{-- The form now calls the unified "save" method --}}
                <form wire:submit.prevent="save" class="mt-6 space-y-6">
                    {{-- Deck Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Deck Name</label>
                        <input wire:model="name" type="text" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Public Toggle --}}
                    <div class="flex items-center">
                        <input wire:model="isPublic" type="checkbox" id="isPublic" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="isPublic" class="ml-2 block text-sm text-gray-900">Make this deck public?</label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="$wire.close()" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            {{-- DYNAMIC BUTTON TEXT --}}
                            @if ($editingDeck)
                                Save Changes
                            @else
                                Create Deck
                            @endif
                        </button>
                    </div>
                 </form>
    </div>
</div>