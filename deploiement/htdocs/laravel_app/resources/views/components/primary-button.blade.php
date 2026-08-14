<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-sky border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-sky-deep focus:bg-brand-sky-deep active:bg-brand-sky-deep focus:outline-none focus:ring-2 focus:ring-brand-sky focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
