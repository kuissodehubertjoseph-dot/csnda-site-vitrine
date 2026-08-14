<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
            Importer des élèves depuis un PDF
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('erreur'))
                <div class="bg-brand-salmon/10 border border-brand-salmon text-brand-salmon-deep text-sm rounded-md px-4 py-3">
                    {{ session('erreur') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-6">
                    Déposez le fichier PDF de la liste de classe fourni par l'école (noms, prénoms et matricules).
                    Vous pourrez vérifier et corriger les données extraites avant leur enregistrement définitif.
                    Les photos et les autres informations (date de naissance, sexe...) restent à compléter ensuite
                    sur la fiche de chaque élève.
                </p>

                <form method="POST" action="{{ route('eleves.import.apercu') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="pdf" value="Fichier PDF de la liste" />
                        <input id="pdf" name="pdf" type="file" accept="application/pdf" required
                            class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-brand-sky file:text-white file:text-sm hover:file:bg-brand-sky-deep">
                        <x-input-error :messages="$errors->get('pdf')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="classe" value="Classe concernée" />
                        <select id="classe" name="classe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach ($classes as $classeOption)
                                <option value="{{ $classeOption }}" @selected(old('classe') === $classeOption)>{{ $classeOption }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('classe')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="annee_scolaire" value="Année scolaire" />
                        <x-text-input id="annee_scolaire" name="annee_scolaire" type="text" class="mt-1 block w-full"
                            :value="old('annee_scolaire', $anneeScolaireCourante)" required />
                        <x-input-error :messages="$errors->get('annee_scolaire')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('eleves.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Annuler</a>
                        <button type="submit" class="px-4 py-2 bg-brand-sky text-white text-sm rounded-md hover:bg-brand-sky-deep">
                            Analyser le fichier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
