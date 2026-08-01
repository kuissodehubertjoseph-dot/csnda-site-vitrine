<x-layouts.public :title="'Galerie photos'">

    <section x-data="{ open: false, src: '', caption: '' }" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-encre uppercase tracking-wide text-center mb-10">
            Galerie photos
        </h1>

        @forelse ($categories as $category)
            @if ($category->photos->isNotEmpty())
                <div class="mb-12">
                    <h2 class="text-lg font-bold text-ciel uppercase tracking-wide mb-4 border-b-2 border-ciel/20 pb-2">
                        {{ $category->name }}
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($category->photos as $photo)
                            <button type="button"
                                    @click="open = true; src = '{{ asset('storage/'.$photo->image) }}'; caption = @js($photo->title)"
                                    class="block group">
                                <img src="{{ asset('storage/'.$photo->image) }}" alt="{{ $photo->title }}"
                                     class="h-32 sm:h-40 w-full object-cover rounded-lg group-hover:opacity-80 transition">
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <p class="text-center text-encre/60">Aucune photo publiée pour le moment.</p>
        @endforelse

        <!-- Lightbox -->
        <div x-show="open" x-cloak
             class="fixed inset-0 z-50 bg-encre/90 flex items-center justify-center p-4"
             @click="open = false" @keydown.escape.window="open = false">
            <div class="max-w-4xl w-full" @click.stop>
                <img :src="src" :alt="caption" class="w-full max-h-[80vh] object-contain rounded-lg">
                <p class="text-white text-center mt-3" x-text="caption"></p>
                <button @click="open = false" class="mt-4 mx-auto block bg-saumon text-white font-bold uppercase text-xs px-6 py-2 rounded-full">
                    Fermer
                </button>
            </div>
        </div>
    </section>

</x-layouts.public>
