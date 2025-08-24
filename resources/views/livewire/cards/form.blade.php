<x-modal name="card-form" title="{{ $editingCard ? 'Edit Card' : 'Add New Card' }}" maxWidth="lg">
    <form wire:submit="save" class="space-y-6">
        {{-- Question --}}
        <div>
            <label for="question-{{ $this->getId() }}" class="block text-sm font-medium text-gray-700">Question</label>
            <textarea wire:model="question" id="question-{{ $this->getId() }}" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                      required autofocus></textarea>
            @error('question') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Answer --}}
        <div>
            <label for="answer-{{ $this->getId() }}" class="block text-sm font-medium text-gray-700">Answer</label>
            <textarea wire:model="answer" id="answer-{{ $this->getId() }}" rows="3"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                      required></textarea>
            @error('answer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Footer Actions --}}
        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" @click="show = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-semibold text-sm">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-500 font-semibold text-sm">
                @if ($editingCard)
                    Save Changes
                @else
                    Add Card
                @endif
            </button>
        </div>
    </form>
</x-modal>