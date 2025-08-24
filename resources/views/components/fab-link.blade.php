@props(['dispatch', 'text', 'deckId'])

<button wire:click="$dispatch('{{ $dispatch }}', {{ !empty($deckId) ? '{ deckId: ' . $deckId . ' }' : '{}' }})"
    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:border-primary-700 focus:ring ring-primary-300 disabled:opacity-25 transition ease-in-out duration-150">
    <i class="fa-solid fa-plus"></i>
    <span class="text-white font-semibold text-md">{{ $text }}</span>
</button>