<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'FlashcardPro' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-gray-100 min-h-screen">
        <nav class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('decks.index') }}" class="font-bold text-xl">FlashcardPro</a>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-4">Welcome, {{ auth()->user()->name }}</span>
                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700">Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-10">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>