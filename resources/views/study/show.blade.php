<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Studying: {{ $deck->name }}
        </h2>
    </x-slot>

    <div class="py-12" id="app">
        {{-- Use the @json directive for safer JSON encoding --}}
        <study-session
            :deck='@json($deck)'
            :requested-card-count='@json($requestedCardCount)'
        ></study-session>
    </div>
</x-app-layout>