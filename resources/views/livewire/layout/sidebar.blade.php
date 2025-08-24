@php
    // We fetch the decks here. For a real app with many decks, you'd paginate or make this searchable.
    $userDecks = auth()->user()->decks()->latest()->take(20)->get();
@endphp

<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
        <div class="flex h-16 shrink-0 items-center">
            <img class="h-10 w-auto" src="{{ asset('images/logo.png') }}" alt="FlashcardPro">
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <div class="text-xs font-semibold leading-6 text-gray-400">Your Decks</div>
                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                        @foreach ($userDecks as $deck)
                            <li>
                                <a href="{{ route('decks.show', $deck) }}"
                                   wire:navigate
                                   class="{{ request()->is('decks/'.$deck->id) ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                    {{ $deck->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li class="mt-auto">
                    <a href="{{ route('profile') }}"
                       class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white">
                        <i class="fa-solid fa-user-circle h-6 w-6 shrink-0"></i>
                        {{ Auth::user()->name }}
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>