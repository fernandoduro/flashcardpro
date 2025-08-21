<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Studying: {{ $deck->name }}
        </h2>
    </x-slot>

    <div class="py-12" id="app">
        <study-session :deck="{{ json_encode($deck) }}"></study-session>
    </div>
</x-layouts.app>