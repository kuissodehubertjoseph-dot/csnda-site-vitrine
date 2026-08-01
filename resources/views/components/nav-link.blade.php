@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-ciel text-sm font-medium leading-5 text-encre focus:outline-none focus:border-ciel transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-encre/60 hover:text-encre/80 hover:border-ciel/30 focus:outline-none focus:text-encre/80 focus:border-ciel/30 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
