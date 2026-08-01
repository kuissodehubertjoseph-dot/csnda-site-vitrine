<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Filières &amp; classes</h1>
            <a href="{{ route('admin.filieres.create') }}" class="bg-laurier text-white text-sm font-bold uppercase px-5 py-2.5 rounded-full hover:bg-laurier/90">
                + Ajouter
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-ciel/10 text-encre uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Ordre</th>
                    <th class="text-left px-4 py-3">Nom</th>
                    <th class="text-left px-4 py-3">Cycle</th>
                    <th class="text-right px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ciel/10">
                @forelse ($filieres as $filiere)
                    <tr>
                        <td class="px-4 py-3 text-encre/70">{{ $filiere->order }}</td>
                        <td class="px-4 py-3 font-semibold text-encre">{{ $filiere->name }}</td>
                        <td class="px-4 py-3 text-encre/70">{{ $filiere->cycle }}</td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.filieres.edit', $filiere) }}" class="text-ciel font-semibold hover:underline">Modifier</a>
                            <form action="{{ route('admin.filieres.destroy', $filiere) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette filière ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-saumon font-semibold hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-encre/60">Aucune filière enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
