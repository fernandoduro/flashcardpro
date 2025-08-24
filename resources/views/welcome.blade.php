<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FLAHSCARDPRO</title>

    <!-- Fonts (Merriweather, as configured in your app.css) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Styles & Scripts (Pulls in Tailwind, Font Awesome, etc.) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .exo-2 {
            font-family: "Exo 2", sans-serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
        }
    </style>
</head>
<body class="antialiased font-sans bg-gray-50">
    <div class="bg-white">
        <!-- Header -->
        <header class="absolute inset-x-0 top-0 z-50">
            <nav class="max-w-7xl mx-auto flex items-center justify-between p-6 lg:px-8" aria-label="Global">
                <div class="flex lg:flex-1">
                    <a href="/" class="-m-1.5 p-1.5 flex items-center space-x-3">
                        <span class="font-bold text-xl text-gray-900 whitespace-nowrap exo-2">FLASHCARDPRO</span>
                    </a>
                </div>
                <div class="flex lg:flex-1 justify-end">
                    @auth
                        <a href="{{ url('/decks') }}" class="text-sm font-semibold leading-6 text-gray-700 hover:text-gray-900">Dashboard <span aria-hidden="true">&rarr;</span></a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-700 hover:text-gray-900">Log in <span aria-hidden="true">&rarr;</span></a>
                    @endauth
                </div>
            </nav>
        </header>

        <main>
            <!-- Hero Section -->
            <div class="relative isolate px-6 pt-14 lg:px-8">
                {{-- Decorative background gradient --}}
                <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                    <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-primary-300 to-primary-600 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
                </div>

                <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
                    <div class="text-center">
                        <h1 class="text-4xl font-black tracking-tight text-gray-900 sm:text-6xl">Unlock Your Potential. Master Any Subject.</h1>
                        <p class="mt-6 text-lg leading-8 text-gray-600">FlashcardPro helps you create, manage, and study digital flashcards with ease. Turn learning into a powerful, effective habit.</p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <a href="{{ route('register') }}" class="rounded-md bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">Get started for free</a>
                            <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-900">Sign in <span aria-hidden="true">→</span></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="bg-gray-50 py-24 sm:py-32">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl lg:text-center">
                        <h2 class="text-base font-semibold leading-7 text-primary-600 uppercase">Learn Smarter</h2>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Everything you need to study effectively</p>
                        <p class="mt-6 text-lg leading-8 text-gray-600">Focus on what matters most with powerful tools designed for efficient learning and retention.</p>
                    </div>
                    <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                        <dl class="grid grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-2">
                            {{-- Feature 1 --}}
                            <div class="flex items-start gap-x-4">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-600">
                                    <i class="fa-solid fa-layer-group text-white fa-lg"></i>
                                </div>
                                <div>
                                    <dt class="text-base font-semibold leading-7 text-gray-900">Create & Organize Decks</dt>
                                    <dd class="mt-1 text-base leading-7 text-gray-600">Effortlessly create flashcards and organize them into beautiful, custom decks with cover images. Your learning, your way.</dd>
                                </div>
                            </div>
                            {{-- Feature 2 --}}
                            <div class="flex items-start gap-x-4">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-600">
                                    <i class="fa-solid fa-brain text-white fa-lg"></i>
                                </div>
                                <div>
                                    <dt class="text-base font-semibold leading-7 text-gray-900">Intelligent Study Sessions</dt>
                                    <dd class="mt-1 text-base leading-7 text-gray-600">Engage in interactive study sessions that help you retain information, with a clean and focused interface powered by Vue.js.</dd>
                                </div>
                            </div>
                            {{-- Feature 3 --}}
                            <div class="flex items-start gap-x-4">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-600">
                                    <i class="fa-solid fa-chart-line text-white fa-lg"></i>
                                </div>
                                <div>
                                    <dt class="text-base font-semibold leading-7 text-gray-900">Track Your Progress</dt>
                                    <dd class="mt-1 text-base leading-7 text-gray-600">Visualize your study habits with detailed statistics. See how many sessions you've completed and identify your most challenging cards.</dd>
                                </div>
                            </div>
                            {{-- Feature 4 --}}
                            <div class="flex items-start gap-x-4">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-600">
                                    <i class="fa-solid fa-code text-white fa-lg"></i>
                                </div>
                                <div>
                                    <dt class="text-base font-semibold leading-7 text-gray-900">Developer-Friendly API</dt>
                                    <dd class="mt-1 text-base leading-7 text-gray-600">Access your public card data through a secure, token-based API, perfect for integrations and custom applications.</dd>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-center py-8">
            <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} FlashcardPro. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>