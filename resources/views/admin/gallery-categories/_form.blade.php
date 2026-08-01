<div class="space-y-6">
    <div>
        <x-input-label for="name" value="Nom de la catégorie (ex: Rentrée scolaire, Sport, Remise des prix...)" />
        <x-text-input id="name" name="name" type="text" class="mt-1 w-full" value="{{ old('name', $category->name ?? '') }}" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="flex gap-3">
        <x-primary-button>Enregistrer</x-primary-button>
        <a href="{{ route('admin.categories-galerie.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-encre/70 hover:text-encre">Annuler</a>
    </div>
</div>
