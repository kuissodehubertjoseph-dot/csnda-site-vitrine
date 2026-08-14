<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
            Impression par lot
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($errors->any())
                <div class="bg-brand-salmon/15 border border-brand-salmon-deep text-brand-salmon-deep text-sm rounded-md px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-4">
                <form method="GET" action="{{ route('cartes.lot.form') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrer par classe</label>
                        <select name="classe" class="rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky" onchange="this.form.submit()">
                            <option value="">Toutes les classes (élèves actifs)</option>
                            @foreach ($classes as $classe)
                                <option value="{{ $classe }}" @selected($classeSelectionnee === $classe)>{{ $classe }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <form method="POST" action="{{ route('cartes.lot.generer') }}" x-data="{ tousCoches: true }">
                @csrf
                <input type="hidden" name="classe" value="{{ $classeSelectionnee }}">

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" x-model="tousCoches"
                                @change="document.querySelectorAll('.case-eleve').forEach(c => c.checked = tousCoches)"
                                class="rounded border-gray-300 text-brand-sky focus:ring-brand-sky">
                            Tout sélectionner / désélectionner
                        </label>
                        <span class="text-sm text-gray-500">{{ $eleves->count() }} élève(s) {{ $classeSelectionnee ? 'dans '.$classeSelectionnee : 'actif(s)' }}</span>
                    </div>

                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-brand-sky/10">
                            <tr>
                                <th class="px-4 py-2 w-8"></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-brand-sky-deep uppercase tracking-wider">Matricule</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-brand-sky-deep uppercase tracking-wider">Nom &amp; prénoms</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-brand-sky-deep uppercase tracking-wider">Classe</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($eleves as $eleve)
                                <tr>
                                    <td class="px-4 py-2">
                                        <input type="checkbox" name="eleves[]" value="{{ $eleve->id }}" checked
                                            class="case-eleve rounded border-gray-300 text-brand-sky focus:ring-brand-sky">
                                    </td>
                                    <td class="px-4 py-2 text-sm font-mono">{{ $eleve->matricule }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $eleve->nom_complet }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $eleve->classe }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">Aucun élève actif trouvé pour ce filtre.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>

                @if ($eleves->isNotEmpty())
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-brand-sky text-white text-sm font-medium rounded-md hover:bg-brand-sky-deep">
                            Générer les cartes (recto + verso)
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-app-layout>
