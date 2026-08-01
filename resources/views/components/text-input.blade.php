@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-ciel/30 focus:border-ciel focus:ring-ciel rounded-md shadow-sm']) }}>
