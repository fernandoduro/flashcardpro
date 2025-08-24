@props(['active'])

@php
$classes = ($active ?? false)
            // Classes for the ACTIVE state
            ? 'inline-flex items-center px-3 py-2 rounded-md text-md font-semibold text-primary uppercase'
            // Classes for the INACTIVE state
            : 'inline-flex items-center px-3 py-2 rounded-md text-md font-semibold text-gray-500 hover:text-gray-700 uppercase';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>