@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-brand-sky focus:ring-brand-sky rounded-md shadow-sm']) }}>
