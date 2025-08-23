<div>
    @if ($showModal)
        <div wire:ignore.self class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" @click.away="$wire.close()">
                <div class="flex flex-col">
                    <div class="flex items-center">
                        {{-- Warning Icon --}}
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 ml-4">{{ $title }}</h3>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm text-gray-500">{{ $message }}</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse -mx-6 -mb-6 mt-6 rounded-b-lg">
                    <button wire:click="confirm" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Delete
                    </button>
                    <button wire:click="close" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>