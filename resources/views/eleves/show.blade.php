<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
                {{ $eleve->nom_complet }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('eleves.edit', $eleve) }}" class="px-4 py-2 bg-white border border-brand-green rounded-md text-sm font-medium text-brand-green-deep hover:bg-brand-green/10">
                    Modifier
                </a>
                <a href="{{ route('cartes.telecharger', $eleve) }}" class="px-4 py-2 bg-brand-sky rounded-md text-sm font-medium text-white hover:bg-brand-sky-deep">
                    Générer la carte (PDF)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('succes'))
                <div class="bg-brand-green/10 border border-brand-green text-brand-green-deep text-sm rounded-md px-4 py-3">
                    {{ session('succes') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-1">
                    <img src="{{ $eleve->photo_url }}" alt="Photo de {{ $eleve->nom_complet }}"
                        class="w-full aspect-square object-cover rounded-lg border-2 border-brand-green">
                </div>

                <div class="sm:col-span-2">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Matricule</dt>
                            <dd class="mt-1 font-mono text-brand-sky-deep font-semibold">{{ $eleve->matricule }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span @class([
                                    'px-2 py-1 rounded-full text-xs font-medium',
                                    'bg-brand-green/15 text-brand-green-deep' => $eleve->statut === 'actif',
                                    'bg-gray-200 text-gray-700' => $eleve->statut === 'inactif',
                                ])>{{ ucfirst($eleve->statut) }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Classe</dt>
                            <dd class="mt-1 text-brand-ink">{{ $eleve->classe }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Année scolaire</dt>
                            <dd class="mt-1 text-brand-ink">{{ $eleve->annee_scolaire }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Date de naissance</dt>
                            <dd class="mt-1 text-brand-ink">
                                @if($eleve->date_naissance)
                                    {{ $eleve->date_naissance->format('d/m/Y') }} ({{ $eleve->age }} ans)
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Lieu de naissance</dt>
                            <dd class="mt-1 text-brand-ink">{{ $eleve->lieu_naissance }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Sexe</dt>
                            <dd class="mt-1 text-brand-ink">{{ $eleve->sexe === 'M' ? 'Masculin' : 'Féminin' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Téléphone</dt>
                            <dd class="mt-1 text-brand-ink">{{ $eleve->telephone ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-serif text-lg text-brand-sky-deep font-semibold mb-4">Aperçu de la carte scolaire</h3>
                <div class="flex flex-wrap gap-6">
                    <div class="w-full max-w-[340px]">
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Recto</p>
                        <iframe src="{{ route('cartes.recto', $eleve) }}" class="border border-gray-200 rounded w-full" style="aspect-ratio: 340 / 214; max-width: 340px;"></iframe>
                    </div>
                    <div class="w-full max-w-[340px]">
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Verso</p>
                        <iframe src="{{ route('cartes.verso', $eleve) }}" class="border border-gray-200 rounded w-full" style="aspect-ratio: 340 / 214; max-width: 340px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
