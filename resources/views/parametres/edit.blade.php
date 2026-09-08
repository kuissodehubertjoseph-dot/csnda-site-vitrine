<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
            Paramètres de la carte scolaire
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('succes'))
                <div class="bg-brand-green/10 border border-brand-green text-brand-green-deep text-sm rounded-md px-4 py-3">
                    {{ session('succes') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-serif text-lg text-brand-sky-deep font-semibold mb-1">Signature du directeur</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Déposez une photo ou un scan de la signature manuscrite du directeur. Elle sera imprimée
                    telle quelle au-dessus de son nom, au verso de la carte scolaire.
                </p>

                <div class="flex items-start gap-6">
                    <div class="flex-none w-40 h-24 border border-gray-200 rounded-md flex items-center justify-center bg-gray-50 overflow-hidden">
                        @if ($signatureUrl)
                            <img src="{{ $signatureUrl }}" alt="Signature du directeur" class="max-w-full max-h-full object-contain">
                        @else
                            <span class="text-xs text-gray-400 text-center px-2">Aucune signature importée</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('parametres.signature') }}" enctype="multipart/form-data" class="flex-1 space-y-3">
                        @csrf
                        <input id="signature" name="signature" type="file" accept="image/*" required
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-brand-sky file:text-white file:text-sm hover:file:bg-brand-sky-deep">
                        <x-input-error :messages="$errors->get('signature')" />
                        <button type="submit" class="px-4 py-2 bg-brand-sky text-white text-sm rounded-md hover:bg-brand-sky-deep">
                            Importer la signature
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-serif text-lg text-brand-sky-deep font-semibold mb-1">Cachet de l'établissement</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Déposez une photo ou un scan du cachet officiel. Il sera imprimé au verso de la carte,
                    à côté de la signature.
                </p>

                <div class="flex items-start gap-6">
                    <div class="flex-none w-40 h-24 border border-gray-200 rounded-md flex items-center justify-center bg-gray-50 overflow-hidden">
                        @if ($cachetUrl)
                            <img src="{{ $cachetUrl }}" alt="Cachet de l'établissement" class="max-w-full max-h-full object-contain">
                        @else
                            <span class="text-xs text-gray-400 text-center px-2">Aucun cachet importé</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('parametres.cachet') }}" enctype="multipart/form-data" class="flex-1 space-y-3">
                        @csrf
                        <input id="cachet" name="cachet" type="file" accept="image/*" required
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-brand-sky file:text-white file:text-sm hover:file:bg-brand-sky-deep">
                        <x-input-error :messages="$errors->get('cachet')" />
                        <button type="submit" class="px-4 py-2 bg-brand-sky text-white text-sm rounded-md hover:bg-brand-sky-deep">
                            Importer le cachet
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
