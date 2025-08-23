{{-- This navigation will only be visible on small screens --}}
<nav class="sm:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 z-50">
    <div class="flex justify-around h-16">
        {{-- Decks Link --}}
        <a href="{{ route('decks.index') }}"
           class="flex flex-col items-center justify-center w-full text-center px-2 {{ request()->routeIs('decks.index') ? 'text-indigo-600' : 'text-gray-500' }} hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v2m14 0h-2M5 11H3" />
            </svg>
            <span class="text-xs mt-1">My Decks</span>
        </a>

        {{-- Statistics Link --}}
        <a href="{{ route('statistics.index') }}"
           class="flex flex-col items-center justify-center w-full text-center px-2 {{ request()->routeIs('statistics.index') ? 'text-indigo-600' : 'text-gray-500' }} hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="text-xs mt-1">Statistics</span>
        </a>
    </div>
</nav>