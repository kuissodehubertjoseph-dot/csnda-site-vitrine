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
                    {{ $nombreEleves }} carte(s) générée(s) avec succès, dans deux fichiers PDF : un avec tous
                    les rectos, un avec tous les versos — pratique pour imprimer une face à la chaîne, retourner
                    la pile, puis imprimer l'autre face.
                </p>

                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('cartes.lot.telecharger', ['lot' => $lot, 'face' => 'recto']) }}"
                        class="px-4 py-2.5 bg-brand-sky text-white text-sm font-medium rounded-md hover:bg-brand-sky-deep">
                        Tous les rectos (PDF)
                    </a>
                    <a href="{{ route('cartes.lot.telecharger', ['lot' => $lot, 'face' => 'verso']) }}"
                        class="px-4 py-2.5 bg-brand-sky text-white text-sm font-medium rounded-md hover:bg-brand-sky-deep">
                        Tous les versos (PDF)
                    </a>
                </div>

                <a href="{{ route('cartes.lot.form') }}" class="inline-block text-sm text-gray-500 hover:text-gray-800 mt-2">
                    ← Retour à la sélection
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
