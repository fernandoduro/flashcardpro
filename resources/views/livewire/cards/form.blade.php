<x-modal name="card-form" title="{{ $editingCard ? 'Edit Card' : 'Add New Card' }}" maxWidth="lg">
    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Question --}}
        <div>
            <label for="question-{{ $this->getId() }}" class="block text-sm font-medium text-gray-700">Question</label>
            <textarea wire:model.blur="question" id="question-{{ $this->getId() }}" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                      required autofocus></textarea>
            @error('question') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Answer --}}
        <div>
            <label for="answer-{{ $this->getId() }}" class="block text-sm font-medium text-gray-700">Answer</label>
            <textarea wire:model.blur="answer" id="answer-{{ $this->getId() }}" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                      required></textarea>
            @error('answer') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
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
                
                {{-- Loading Spinner --}}
                <div wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Button Text --}}
                <span>
                    @if ($editingCard)
                        Save Changes
                    @else
                        Add Card
                    @endif
                </span>
            </button>
        </div>
    </form>
</x-modal>