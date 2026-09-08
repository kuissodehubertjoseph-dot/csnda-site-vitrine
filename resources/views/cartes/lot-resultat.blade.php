<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
            Cartes générées
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 text-center space-y-4">
                <p class="text-gray-700">
                    {{ $nombreEleves }} carte(s) générée(s) avec succès, réparties en {{ $nombrePaquets }} paquet(s)
                    de 25 cartes maximum chacun. Chaque paquet donne deux fichiers PDF séparés : un pour les rectos,
                    un pour les versos — pratique pour imprimer une face à la chaîne, retourner la pile, puis
                    imprimer l'autre face.
                </p>

                <div class="divide-y divide-gray-100">
                    @for ($i = 0; $i < $nombrePaquets; $i++)
                        <div class="py-3 flex items-center justify-between gap-4">
                            <span class="text-sm font-medium text-gray-600">Paquet {{ $i + 1 }}</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('cartes.lot.telecharger', ['lot' => $lot, 'face' => 'recto', 'paquet' => $i]) }}"
                                    class="px-4 py-2.5 bg-brand-sky text-white text-sm font-medium rounded-md hover:bg-brand-sky-deep">
                                    Rectos (PDF)
                                </a>
                                <a href="{{ route('cartes.lot.telecharger', ['lot' => $lot, 'face' => 'verso', 'paquet' => $i]) }}"
                                    class="px-4 py-2.5 bg-brand-sky text-white text-sm font-medium rounded-md hover:bg-brand-sky-deep">
                                    Versos (PDF)
                                </a>
                            </div>
                        </div>
                    @endfor
                </div>

                <a href="{{ route('cartes.lot.form') }}" class="inline-block text-sm text-gray-500 hover:text-gray-800 mt-2">
                    ← Retour à la sélection
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
