@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-encre']) }}>
    {{ $value ?? $slot }}
</label>
