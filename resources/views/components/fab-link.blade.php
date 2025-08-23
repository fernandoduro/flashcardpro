@props(['dispatch', 'text', 'deckId'])

<button wire:click="$dispatch('{{ $dispatch }}', {{ !empty($deckId) ? '{ deckId: ' . $deckId . ' }' : '{}' }})"
        class="fixed bottom-8 right-8 z-50 flex items-center justify-center w-auto h-14 px-6 bg-indigo-600 rounded-full shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-transform transform hover:scale-105">
    <svg xmlns="http://www.w.org/2000/svg" class="h-6 w-6 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
    <span class="text-white font-semibold text-lg">{{ $text }}</span>
</button>