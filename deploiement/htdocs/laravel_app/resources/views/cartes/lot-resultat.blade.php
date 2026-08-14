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
                    {{ $nombreEleves }} carte(s) générée(s) avec succès. Téléchargez les deux fichiers ci-dessous
                    pour l'impression (un PDF multi-pages pour les rectos, un pour les versos, dans le même ordre).
                </p>

                <div class="flex justify-center gap-4">
                    <a href="{{ route('cartes.lot.telecharger', ['lot' => $lot, 'type' => 'recto']) }}"
                        class="px-5 py-2.5 bg-brand-sky text-white text-sm font-medium rounded-md hover:bg-brand-sky-deep">
                        Télécharger les rectos (PDF)
                    </a>
                    <a href="{{ route('cartes.lot.telecharger', ['lot' => $lot, 'type' => 'verso']) }}"
                        class="px-5 py-2.5 bg-white border border-brand-green text-brand-green-deep text-sm font-medium rounded-md hover:bg-brand-green/10">
                        Télécharger les versos (PDF)
                    </a>
                </div>

                <a href="{{ route('cartes.lot.form') }}" class="inline-block text-sm text-gray-500 hover:text-gray-800 mt-2">
                    ← Retour à la sélection
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
