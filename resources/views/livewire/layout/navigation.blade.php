<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            
            <!-- Center: Logo -->
            <div class="flex-shrink-0 flex items-center justify-start w-1/3">
                <a class="flex items-center text-3xl justify-center exo-2 whitespace-nowrap" href="{{ route('decks.index') }}">
                    FLASHCARDPRO 
                </a>
            </div>
            
            <!-- Left Side: Tabs -->
            <div class="hidden sm:flex sm:items-center justify-end sm:w-1/3">
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('decks.index')" :active="request()->routeIs('decks.index')">
                        {{ __('Decks') }}
                    </x-nav-link>
                    <x-nav-link :href="route('statistics.index')" :active="request()->routeIs('statistics.index')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('profile')" :active="request()->routeIs('profile')">
                        {{ __('Profile') }}
                    </x-nav-link>
                    <x-nav-link class="cursor-pointer hover:text-red-600" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                         {{ __('Exit') }} <fa class="fa-solid fa-right-from-bracket ml-2"></fa>
                    </x-nav-link>
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                 <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                         <x-dropdown-link :href="route('profile')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <
                        <x-dropdown-link onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Exit') }}
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
    <!-- Authentication -->
    <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
        @csrf
    </form>
</nav>
