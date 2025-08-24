@props([
    'name',
    'title' => '',
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail == '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail == '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
    style="display: none;"
>
    <!-- Full-screen backdrop -->
    <div x-show="show" x-transition.opacity
         class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm">
    </div>

    <!-- Modal Panel -->
    <div x-show="show" x-transition
         @click.away="show = false"
         class="relative w-full bg-white rounded-lg shadow-xl {{ $maxWidth }}">

        <!-- Modal Header -->
        <div class="flex items-start justify-between p-4 border-b rounded-t">
            <h3 class="text-xl font-bold text-gray-900">{{ $title }}</h3>
            <button @click="show = false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                <i class="fa-solid fa-xmark fa-lg"></i>
                <span class="sr-only">Close modal</span>
            </button>
        </div>

        <!-- Modal Body (Your Form) -->
        <div class="p-6 space-y-6">
            {{ $slot }}
        </div>
    </div>
</div>