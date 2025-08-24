<x-modal name="confirmation-modal" :title="$title" maxWidth="md">
    <div class="flex items-start space-x-4">
        {{-- Warning Icon --}}
        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>

        {{-- Message --}}
        <div class="flex-1">
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
            <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="flex justify-end space-x-4 mt-6">
        <button type="button" @click="show = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 font-semibold text-sm">
            Cancel
        </button>
        <button type="button" wire:click="confirm" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-semibold text-sm">
            Confirm Delete
        </button>
    </div>
</x-modal>