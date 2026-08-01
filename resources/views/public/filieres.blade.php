<x-layouts.public :title="'Filières & classes'">

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-encre uppercase tracking-wide text-center mb-4">
            Filières &amp; classes
        </h1>
        <p class="text-center text-encre/70 max-w-2xl mx-auto mb-10">
            Du Cours Primaire à la Terminale, le CSNDA accompagne chaque élève tout au long de son parcours scolaire.
        </p>

        @if ($filieres->isEmpty())
            <p class="text-center text-encre/60">Les informations sur les filières seront bientôt disponibles.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($filieres as $filiere)
                    <div class="bg-white border-2 border-ciel/20 rounded-2xl p-6 hover:border-ciel hover:shadow-lg transition">
                        @if ($filiere->cycle)
                            <span class="inline-block text-xs font-bold uppercase tracking-wide text-white bg-laurier rounded-full px-3 py-1 mb-3">{{ $filiere->cycle }}</span>
                        @endif
                        <h2 class="font-extrabold text-ciel text-lg mb-2">{{ $filiere->name }}</h2>
                        @if ($filiere->description)
                            <p class="text-sm text-encre/70">{{ $filiere->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</x-layouts.public>
