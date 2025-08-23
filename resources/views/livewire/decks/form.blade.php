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

                    <div>
                        <label for="coverImage" class="block text-sm font-medium text-gray-700">Cover Image</label>
                        <div class="mt-2 flex items-center space-x-4">
                            {{-- Image Preview --}}
                            <div class="flex-shrink-0">
                                @if ($coverImage)
                                    {{-- Temporary preview of new upload --}}
                                    <img class="h-20 w-20 rounded-md object-cover" src="{{ $coverImage->temporaryUrl() }}" alt="New Cover Preview">
                                @elseif ($editingDeck && $editingDeck->cover_image_path)
                                    {{-- Existing cover image --}}
                                    <img class="h-20 w-20 rounded-md object-cover" src="{{ asset('storage/' . $editingDeck->cover_image_path) }}" alt="{{ $editingDeck->name }}">
                                @else
                                    {{-- Placeholder --}}
                                    <div class="h-20 w-20 bg-gray-200 rounded-md flex items-center justify-center">
                                        <svg class="h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <input type="file" wire:model="coverImage" id="coverImage" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <div wire:loading wire:target="coverImage" class="text-sm text-gray-500 mt-1">Uploading...</div>
                            </div>
                        </div>
                        @error('coverImage') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
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