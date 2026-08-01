@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-laurier']) }}>
        {{ $status }}
    </div>
@endif
