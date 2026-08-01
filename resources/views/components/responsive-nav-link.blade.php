@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-ciel text-start text-base font-medium text-ciel bg-ciel/10 focus:outline-none focus:text-ciel focus:bg-ciel/20 focus:border-ciel transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-encre/70 hover:text-encre hover:bg-ciel/5 hover:border-ciel/30 focus:outline-none focus:text-encre focus:bg-ciel/5 focus:border-ciel/30 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
