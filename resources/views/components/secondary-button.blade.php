<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-ciel/30 rounded-md font-semibold text-xs text-encre/80 uppercase tracking-widest shadow-sm hover:bg-ciel/5 focus:outline-none focus:ring-2 focus:ring-ciel focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
