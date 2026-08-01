<x-layouts.public :title="$news->title">

    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <p class="text-xs font-semibold text-laurier uppercase mb-2 text-center">{{ $news->published_at->translatedFormat('d F Y') }}</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-encre text-center mb-8">{{ $news->title }}</h1>

        @if ($news->image)
            <img src="{{ asset('storage/'.$news->image) }}" alt="{{ $news->title }}" class="w-full h-72 object-cover rounded-2xl mb-8">
        @endif

        <div class="prose max-w-none text-encre/90 leading-relaxed">
            {!! nl2br(e($news->content)) !!}
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('news.index') }}" class="text-ciel font-semibold hover:underline">&larr; Retour aux actualités</a>
        </div>
    </article>

    @if ($recent->isNotEmpty())
    <section class="bg-ciel/5 py-14">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-extrabold text-encre uppercase tracking-wide text-center mb-8">Autres actualités</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ($recent as $item)
                    <a href="{{ route('news.show', $item) }}" class="bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition">
                        @if ($item->image)
                            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="h-36 w-full object-cover">
                        @endif
                        <div class="p-4">
                            <h3 class="font-bold text-encre text-sm">{{ $item->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</x-layouts.public>
