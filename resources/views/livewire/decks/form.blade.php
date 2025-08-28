<x-modal name="deck-form" title="{{ $editingDeck ? 'Edit Deck' : 'Create New Deck' }}" maxWidth="lg">
    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Deck Name --}}
        <div>
            <label for="name-{{ $this->getId() }}" class="block text-sm font-medium text-gray-700">Deck Name</label>

            <x-text-input wire:model.blur="name" type="text" id="name-{{ $this->getId() }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required autofocus />
            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Public Toggle --}}
        <div class="flex items-center">
            <x-text-input wire:model="isPublic" type="checkbox" id="isPublic-{{ $this->getId() }}" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" />
            <label for="isPublic-{{ $this->getId() }}" class="ml-3 block text-sm font-medium text-gray-700">Make this deck public?</label>
        </div>

        {{-- Cover Image --}}
        <div>
            <label for="coverImage-{{ $this->getId() }}" class="block text-sm font-medium text-gray-700">Cover Image</label>
            <div class="mt-2 flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @if ($coverImage)
                        <img class="h-20 w-20 rounded-md object-cover" src="{{ $coverImage->temporaryUrl() }}" alt="New Cover Preview">
                    @elseif ($editingDeck && $editingDeck->cover_image_path)
                        <img class="h-20 w-20 rounded-md object-cover" src="{{ asset('storage/' . $editingDeck->cover_image_path) }}" alt="{{ $editingDeck->name }}">
                    @else
                        <div class="h-20 w-20 bg-gray-200 rounded-md flex items-center justify-center text-gray-400">
                            <i class="fa-solid fa-image fa-2x"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <x-text-input type="file" wire:model="coverImage" id="coverImage-{{ $this->getId() }}" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                    <div wire:loading wire:target="coverImage" class="text-sm text-gray-500 mt-1">Uploading...</div>
                </div>
            </div>
            @error('coverImage') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Footer Actions --}}
        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" @click="show = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 font-semibold text-sm">
                Cancel
            </button>
            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-500 font-semibold text-sm flex items-center disabled:opacity-75 disabled:cursor-not-allowed">
                
                {{-- Loading Spinner for the save action --}}
                <div wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Button Text --}}
                <span>
                    @if ($editingDeck)
                        Save Changes
                    @else
                        Create Deck
                    @endif
                </span>
            </button>
        </div>
    </form>
</x-modal>