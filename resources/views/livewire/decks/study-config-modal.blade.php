<x-modal name="study-config" title="Study Configuration" maxWidth="md">
    <form wire:submit.prevent="startStudySession" class="space-y-6">
        {{-- Card Count Input --}}
        <div>
            <label for="study-card-count" class="block text-sm font-medium text-gray-700 mb-2">
                How many cards would you like to study?
            </label>
            <p class="text-sm text-gray-500 mb-4">
                Maximum: {{ $deck->cards->count() }} cards
            </p>
            <x-text-input
                wire:model.blur="studyCardCount"
                id="study-card-count"
                type="number"
                min="1"
                max="{{ $deck->cards->count() }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-100 disabled:text-gray-500"
                required
                autofocus
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            />
            @error('studyCardCount')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
            <p class="text-xs text-gray-500 mt-1">
                Enter a number between 1 and {{ $deck->cards->count() }}
            </p>
        </div>

        {{-- Footer Actions --}}
        <div class="flex justify-end space-x-4 pt-4">
            <button type="button" @click="show = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 font-semibold text-sm">
                Cancel
            </button>
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="startStudySession"
                {{ $deck->cards->count() === 0 ? 'disabled' : '' }}
                class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-500 font-semibold text-sm flex items-center disabled:opacity-75 disabled:cursor-not-allowed"
            >
                {{-- Loading Spinner --}}
                <div wire:loading wire:target="startStudySession" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Button Text --}}
                <span>Start Study Session</span>
            </button>
        </div>
    </form>
</x-modal>
