{{-- This navigation will only be visible on small screens --}}
<nav class="sm:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 z-50">
    <div class="flex justify-around h-16">
        {{-- Decks Link --}}
        <a href="{{ route('decks.index') }}" wire:navigate
           class="flex flex-col items-center justify-center w-full text-center px-2 transition-colors
                  {{ request()->routeIs('decks.index*') ? 'text-primary-600' : 'text-gray-500 hover:text-primary-600 hover:bg-gray-50' }}">
            {{-- Replaced SVG with Font Awesome icon --}}
            <i class="fa-solid fa-layer-group fa-lg mb-2"></i>
            <span class="text-xs mt-1 font-medium">Dashboard</span>
        </a>

        {{-- Statistics Link --}}
        <a href="{{ route('statistics.index') }}" wire:navigate
           class="flex flex-col items-center justify-center w-full text-center px-2 transition-colors
                  {{ request()->routeIs('statistics.index') ? 'text-primary-600' : 'text-gray-500 hover:text-primary-600 hover:bg-gray-50' }}">
            {{-- Replaced SVG with Font Awesome icon --}}
            <i class="fa-solid fa-chart-line fa-lg mb-2"></i>
            <span class="text-xs mt-1 font-medium">Statistics</span>
        </a>
    </div>
</nav>