<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Photos de la galerie</h1>
            <a href="{{ route('admin.photos-galerie.create') }}" class="bg-laurier text-white text-sm font-bold uppercase px-5 py-2.5 rounded-full hover:bg-laurier/90">
                + Ajouter une photo
            </a>
        </div>
    </x-slot>

    @if ($photos->isEmpty())
        <p class="text-center text-encre/60 bg-white rounded-2xl shadow p-10">Aucune photo pour le moment.</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($photos as $photo)
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <img src="{{ asset('storage/'.$photo->image) }}" alt="{{ $photo->title }}" class="h-32 w-full object-cover">
                    <div class="p-3">
                        <p class="text-xs font-semibold text-encre truncate">{{ $photo->title ?: 'Sans titre' }}</p>
                        <p class="text-xs text-ciel">{{ $photo->category?->name ?? 'Sans catégorie' }}</p>
                        <form action="{{ route('admin.photos-galerie.destroy', $photo) }}" method="POST" class="mt-2" onsubmit="return confirm('Supprimer cette photo ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-saumon text-xs font-semibold hover:underline">Supprimer</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $photos->links() }}</div>
    @endif
</x-app-layout>
