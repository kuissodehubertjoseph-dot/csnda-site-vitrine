<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Ajouter une photo</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow p-6 max-w-xl">
        <form method="POST" action="{{ route('admin.photos-galerie.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="gallery_category_id" value="Catégorie" />
                <select id="gallery_category_id" name="gallery_category_id" class="mt-1 w-full rounded-md border-ciel/30 focus:border-ciel focus:ring-ciel">
                    <option value="">— Sans catégorie —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('gallery_category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('gallery_category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="title" value="Titre (optionnel)" />
                <x-text-input id="title" name="title" type="text" class="mt-1 w-full" value="{{ old('title') }}" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="image" value="Photo" />
                <input id="image" name="image" type="file" accept="image/*" required class="mt-1 w-full text-sm">
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <div class="flex gap-3">
                <x-primary-button>Ajouter</x-primary-button>
                <a href="{{ route('admin.photos-galerie.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-encre/70 hover:text-encre">Annuler</a>
            </div>
        </form>
    </div>
</x-app-layout>
