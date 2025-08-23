<div
    x-data="{ show: false }"
    x-on:open-card-modal.window="show = true"
    x-on:close-card-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50"
    style="display: none;"
>
    {{-- Modal backdrop --}}
    <div  x-show="show" x-transition.opacity  @mousedown="show = false" class="fixed inset-0 bg-gray-800 bg-opacity-75"></div>

    {{-- Modal content --}}
    <div x-show="show" x-transition @mousedown.stop
         class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg mx-auto my-8 relative">

        <div class="flex justify-between items-center pb-3 border-b">
            {{-- DYNAMIC TITLE --}}
            <h3 class="text-2xl font-bold">
                @if ($editingCard)
                    Edit Card
                @else
                    Add New Card to "{{ $deck?->name }}"
                @endif
            </h3>
            <button @click="show = false" class="text-gray-500 hover:text-gray-700 text-3xl leading-none">&times;</button>
        </div>

        <form wire:submit.prevent="save" class="mt-6 space-y-6">
            {{-- Question --}}
            <div>
                <label for="question" class="block text-sm font-medium text-gray-700">Question</label>
                <textarea wire:model="question" id="question" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                @error('question') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Answer --}}
            <div>
                <label for="answer" class="block text-sm font-medium text-gray-700">Answer</label>
                <textarea wire:model="answer" id="answer" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                @error('answer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex justify-end space-x-4 pt-4">
                <button type="button" @click="show = false" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    @if ($editingCard)
                        Save Changes
                    @else
                        Add Card
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>