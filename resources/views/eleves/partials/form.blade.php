@php
    $eleve = $eleve ?? null;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-1" data-photo-crop>
        <label class="block text-sm font-medium text-gray-700 mb-2">Photo de l'élève</label>

        <div class="relative w-full aspect-square rounded-lg border-2 border-dashed border-brand-green bg-gray-50 overflow-hidden flex items-center justify-center">
            <canvas data-photo-canvas class="hidden w-full h-full cursor-move"></canvas>

            <div data-photo-placeholder class="text-center p-4">
                @if ($eleve?->photo)
                    <img src="{{ $eleve->photo_url }}" alt="Photo actuelle" class="mx-auto h-32 w-32 rounded object-cover border border-brand-green">
                    <p class="text-xs text-gray-500 mt-2">Photo actuelle — choisissez un fichier pour la remplacer</p>
                @else
                    <p class="text-sm text-gray-500">Aucune photo sélectionnée</p>
                @endif
            </div>
        </div>

        <input type="range" data-photo-zoom class="hidden w-full mt-2">

        <label class="mt-3 inline-flex items-center px-4 py-2 bg-white border border-brand-green rounded-md text-sm font-medium text-brand-green-deep hover:bg-brand-green/10 cursor-pointer">
            Choisir une photo
            <input type="file" name="photo" accept="image/*" data-photo-input class="sr-only">
        </label>
        <p class="text-xs text-gray-500 mt-1">Glissez pour repositionner, utilisez le curseur pour zoomer.</p>

        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
    </div>

    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <x-input-label for="matricule" value="N° Matricule" />
            <x-text-input id="matricule" name="matricule" type="text" class="mt-1 block w-full font-mono"
                :value="old('matricule', $eleve?->matricule)" placeholder="Ex : 1130823020629" required />
            <p class="text-xs text-gray-500 mt-1">Matricule officiel de l'élève, tel qu'il figure sur la liste de l'établissement.</p>
            <x-input-error :messages="$errors->get('matricule')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="nom" value="Nom" />
            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $eleve?->nom)" required autofocus />
            <x-input-error :messages="$errors->get('nom')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="prenoms" value="Prénoms" />
            <x-text-input id="prenoms" name="prenoms" type="text" class="mt-1 block w-full" :value="old('prenoms', $eleve?->prenoms)" required />
            <x-input-error :messages="$errors->get('prenoms')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="date_naissance" value="Date de naissance" />
            <x-text-input id="date_naissance" name="date_naissance" type="date" class="mt-1 block w-full"
                :value="old('date_naissance', $eleve?->date_naissance?->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('date_naissance')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="lieu_naissance" value="Lieu de naissance" />
            <x-text-input id="lieu_naissance" name="lieu_naissance" type="text" class="mt-1 block w-full" :value="old('lieu_naissance', $eleve?->lieu_naissance)" required />
            <x-input-error :messages="$errors->get('lieu_naissance')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="sexe" value="Sexe" />
            <select id="sexe" name="sexe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky" required>
                <option value="">-- Sélectionner --</option>
                <option value="M" @selected(old('sexe', $eleve?->sexe) === 'M')>Masculin</option>
                <option value="F" @selected(old('sexe', $eleve?->sexe) === 'F')>Féminin</option>
            </select>
            <x-input-error :messages="$errors->get('sexe')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="classe" value="Classe" />
            <select id="classe" name="classe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky" required>
                <option value="">-- Sélectionner --</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe }}" @selected(old('classe', $eleve?->classe) === $classe)>{{ $classe }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('classe')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="telephone" value="Téléphone (parent/tuteur)" />
            <x-text-input id="telephone" name="telephone" type="text" class="mt-1 block w-full" :value="old('telephone', $eleve?->telephone)" />
            <x-input-error :messages="$errors->get('telephone')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="annee_scolaire" value="Année scolaire" />
            <x-text-input id="annee_scolaire" name="annee_scolaire" type="text" class="mt-1 block w-full"
                :value="old('annee_scolaire', $eleve?->annee_scolaire ?? $anneeScolaireCourante ?? config('ecole.annee_scolaire_courante'))" required />
            <x-input-error :messages="$errors->get('annee_scolaire')" class="mt-1" />
        </div>

        <div class="sm:col-span-2">
            <x-input-label for="signature" value="Signature de l'élève" />
            <p class="text-xs text-gray-500 mb-2">
                Photo ou scan de la signature manuscrite de l'élève. Elle sera imprimée sur le recto de la
                carte scolaire, au-dessus de la mention « Le (La) Titulaire ».
            </p>

            <div class="flex items-center gap-4">
                @if ($eleve?->signature)
                    <div class="flex-none w-32 h-16 border border-gray-200 rounded-md bg-gray-50 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('storage/'.$eleve->signature) }}" alt="Signature actuelle" class="max-w-full max-h-full object-contain">
                    </div>
                @endif
                <input id="signature" name="signature" type="file" accept="image/*"
                    class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-brand-sky file:text-white file:text-sm hover:file:bg-brand-sky-deep">
            </div>
            <x-input-error :messages="$errors->get('signature')" class="mt-1" />
        </div>

        @if ($eleve)
            <div>
                <x-input-label for="statut" value="Statut" />
                <select id="statut" name="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky">
                    <option value="actif" @selected(old('statut', $eleve->statut) === 'actif')>Actif</option>
                    <option value="inactif" @selected(old('statut', $eleve->statut) === 'inactif')>Inactif</option>
                </select>
                <x-input-error :messages="$errors->get('statut')" class="mt-1" />
            </div>
        @endif
    </div>
</div>
