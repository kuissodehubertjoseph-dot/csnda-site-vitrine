<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Contenu des pages</h1>
    </x-slot>

    <div class="mb-6 flex gap-2 flex-wrap">
        @foreach (['presentation' => 'Présentation', 'historique' => 'Historique', 'mot-directeur' => 'Mot du directeur'] as $slug => $label)
            <a href="{{ route('admin.pages.edit', $slug) }}"
               class="px-4 py-2 rounded-full text-sm font-semibold {{ $page->slug === $slug ? 'bg-ciel text-white' : 'bg-white text-encre border border-ciel/30' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl shadow p-6">
        <form method="POST" action="{{ route('admin.pages.update', $page->slug) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="title" value="Titre de la section" />
                <x-text-input id="title" name="title" type="text" class="mt-1 w-full" value="{{ old('title', $page->title) }}" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="content" value="Texte" />
                <textarea id="content" name="content" rows="10" class="mt-1 w-full rounded-md border-ciel/30 focus:border-ciel focus:ring-ciel">{{ old('content', $page->content) }}</textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="image" :value="$page->slug === 'mot-directeur' ? 'Photo du directeur' : 'Image (optionnelle)'" />
                @if ($page->image)
                    <img src="{{ asset('storage/'.$page->image) }}" alt="{{ $page->title }}" class="h-32 w-32 object-cover rounded-lg mt-2 mb-2">
                @endif
                <input id="image" name="image" type="file" accept="image/*" class="mt-1 w-full text-sm">
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>

            <x-primary-button>Enregistrer les modifications</x-primary-button>
        </form>
    </div>
</x-app-layout>
