<div class="space-y-6">
    <div>
        <x-input-label for="name" value="Nom (ex: 6ème, Terminale D...)" />
        <x-text-input id="name" name="name" type="text" class="mt-1 w-full" value="{{ old('name', $filiere->name ?? '') }}" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cycle" value="Cycle (ex: Primaire, Premier cycle, Second cycle)" />
        <x-text-input id="cycle" name="cycle" type="text" class="mt-1 w-full" value="{{ old('cycle', $filiere->cycle ?? '') }}" />
        <x-input-error :messages="$errors->get('cycle')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border-ciel/30 focus:border-ciel focus:ring-ciel">{{ old('description', $filiere->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="order" value="Ordre d'affichage" />
        <x-text-input id="order" name="order" type="number" min="0" class="mt-1 w-32" value="{{ old('order', $filiere->order ?? 0) }}" />
        <x-input-error :messages="$errors->get('order')" class="mt-2" />
    </div>

    <div class="flex gap-3">
        <x-primary-button>Enregistrer</x-primary-button>
        <a href="{{ route('admin.filieres.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-encre/70 hover:text-encre">Annuler</a>
    </div>
</div>
