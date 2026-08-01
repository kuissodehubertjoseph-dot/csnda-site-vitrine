<x-layouts.public :title="'Actualités & événements'">

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-encre uppercase tracking-wide text-center mb-10">
            Actualités &amp; événements
        </h1>

        @if ($news->isEmpty())
            <p class="text-center text-encre/60">Aucune actualité publiée pour le moment.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($news as $item)
                    <a href="{{ route('news.show', $item) }}" class="bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition border border-ciel/10">
                        @if ($item->image)
                            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="h-44 w-full object-cover">
                        @else
                            <div class="h-44 w-full bg-ciel/20 flex items-center justify-center text-ciel font-bold">CSNDA</div>
                        @endif
                        <div class="p-5">
                            <p class="text-xs font-semibold text-laurier uppercase mb-1">{{ $item->published_at->translatedFormat('d F Y') }}</p>
                            <h2 class="font-bold text-encre mb-1">{{ $item->title }}</h2>
                            @if ($item->excerpt)
                                <p class="text-sm text-encre/70 line-clamp-2">{{ $item->excerpt }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $news->links() }}
            </div>
        @endif
    </section>

</x-layouts.public>
