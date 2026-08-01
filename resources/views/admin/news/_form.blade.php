<div class="space-y-6">
    <div>
        <x-input-label for="title" value="Titre" />
        <x-text-input id="title" name="title" type="text" class="mt-1 w-full" value="{{ old('title', $news->title ?? '') }}" required />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="published_at" value="Date de publication" />
        <x-text-input id="published_at" name="published_at" type="date" class="mt-1 w-full sm:w-64" value="{{ old('published_at', isset($news) ? $news->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}" required />
        <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="excerpt" value="Résumé court (affiché dans les listes)" />
        <textarea id="excerpt" name="excerpt" rows="2" class="mt-1 w-full rounded-md border-ciel/30 focus:border-ciel focus:ring-ciel">{{ old('excerpt', $news->excerpt ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="content" value="Contenu complet" />
        <textarea id="content" name="content" rows="10" class="mt-1 w-full rounded-md border-ciel/30 focus:border-ciel focus:ring-ciel" required>{{ old('content', $news->content ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('content')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="image" value="Image" />
        @if (isset($news) && $news->image)
            <img src="{{ asset('storage/'.$news->image) }}" alt="{{ $news->title }}" class="h-32 w-full max-w-xs object-cover rounded-lg mt-2 mb-2">
        @endif
        <input id="image" name="image" type="file" accept="image/*" class="mt-1 w-full text-sm">
        <x-input-error :messages="$errors->get('image')" class="mt-2" />
    </div>

    <div class="flex gap-3">
        <x-primary-button>Enregistrer</x-primary-button>
        <a href="{{ route('admin.actualites.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-encre/70 hover:text-encre">Annuler</a>
    </div>
</div>
