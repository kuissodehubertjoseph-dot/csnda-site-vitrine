<x-layouts.public :title="'Accueil'">

    <!-- Bandeau principal -->
    <section class="bg-gradient-to-b from-ciel/15 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 text-center">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo CSNDA" class="h-32 w-32 sm:h-40 sm:w-40 mx-auto mb-6">
            <h1 class="text-2xl sm:text-4xl font-extrabold text-encre uppercase tracking-wide">
                Cours Secondaire Notre-Dame des Apôtres
            </h1>
            <p class="mt-3 text-lg sm:text-xl font-semibold italic text-laurier">
                &laquo; Optimus esse aut non esse &raquo;
            </p>
            <p class="mt-6 max-w-2xl mx-auto text-base sm:text-lg text-encre/80">
                {{ $accroche }}
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact') }}" class="bg-laurier text-white font-bold uppercase text-sm px-8 py-3 rounded-full hover:bg-laurier/90 transition">
                    Nous contacter
                </a>
                <a href="{{ route('presentation') }}" class="bg-white border-2 border-ciel text-ciel font-bold uppercase text-sm px-8 py-3 rounded-full hover:bg-ciel hover:text-white transition">
                    Découvrir l'école
                </a>
            </div>
        </div>
    </section>

    <!-- Aperçu rapide des sections -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 sm:grid-cols-3 gap-8">
        <a href="{{ route('filieres') }}" class="group rounded-2xl border-2 border-ciel/30 p-6 text-center hover:border-ciel hover:shadow-lg transition">
            <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-ciel/10 flex items-center justify-center text-ciel text-2xl font-bold">01</div>
            <h2 class="font-bold text-encre uppercase text-sm tracking-wide mb-2">Filières &amp; classes</h2>
            <p class="text-sm text-encre/70">Du Cours Primaire à la Terminale, découvrez notre offre pédagogique.</p>
        </a>
        <a href="{{ route('news.index') }}" class="group rounded-2xl border-2 border-laurier/30 p-6 text-center hover:border-laurier hover:shadow-lg transition">
            <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-laurier/10 flex items-center justify-center text-laurier text-2xl font-bold">02</div>
            <h2 class="font-bold text-encre uppercase text-sm tracking-wide mb-2">Actualités &amp; événements</h2>
            <p class="text-sm text-encre/70">Suivez la vie de l'établissement et ses événements marquants.</p>
        </a>
        <a href="{{ route('gallery') }}" class="group rounded-2xl border-2 border-saumon/40 p-6 text-center hover:border-saumon hover:shadow-lg transition">
            <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-saumon/20 flex items-center justify-center text-saumon text-2xl font-bold">03</div>
            <h2 class="font-bold text-encre uppercase text-sm tracking-wide mb-2">Galerie photos</h2>
            <p class="text-sm text-encre/70">Revivez en images les moments forts de la vie scolaire.</p>
        </a>
    </section>

    <!-- Actualités récentes -->
    @if ($news->isNotEmpty())
    <section class="bg-ciel/5 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl sm:text-2xl font-extrabold text-encre uppercase tracking-wide">Actualités récentes</h2>
                <a href="{{ route('news.index') }}" class="text-sm font-semibold text-ciel hover:underline">Voir tout &rarr;</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($news as $item)
                    <a href="{{ route('news.show', $item) }}" class="bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition">
                        @if ($item->image)
                            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" class="h-44 w-full object-cover">
                        @else
                            <div class="h-44 w-full bg-ciel/20 flex items-center justify-center text-ciel font-bold">CSNDA</div>
                        @endif
                        <div class="p-5">
                            <p class="text-xs font-semibold text-laurier uppercase mb-1">{{ $item->published_at->translatedFormat('d F Y') }}</p>
                            <h3 class="font-bold text-encre">{{ $item->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Aperçu galerie -->
    @if ($photos->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl sm:text-2xl font-extrabold text-encre uppercase tracking-wide">Galerie</h2>
            <a href="{{ route('gallery') }}" class="text-sm font-semibold text-ciel hover:underline">Voir tout &rarr;</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach ($photos as $photo)
                <img src="{{ asset('storage/'.$photo->image) }}" alt="{{ $photo->title }}" class="h-32 sm:h-40 w-full object-cover rounded-lg">
            @endforeach
        </div>
    </section>
    @endif

    <!-- Bouton contact -->
    <section class="bg-laurier">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
            <h2 class="text-xl sm:text-2xl font-extrabold text-white uppercase tracking-wide mb-4">Une question ? Contactez-nous</h2>
            <a href="{{ route('contact') }}" class="inline-block bg-white text-laurier font-bold uppercase text-sm px-8 py-3 rounded-full hover:bg-saumon hover:text-white transition">
                Accéder au formulaire de contact
            </a>
        </div>
    </section>

</x-layouts.public>
