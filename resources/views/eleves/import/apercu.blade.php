<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
            Vérifier les élèves détectés
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow-sm rounded-lg p-4 text-sm text-gray-600">
                <strong>{{ count($lignes) }}</strong> ligne(s) détectée(s) pour la classe
                <strong>{{ $classe }}</strong> — année scolaire <strong>{{ $anneeScolaire }}</strong>.
                Corrigez les champs si besoin, décochez les lignes à ignorer, puis validez.
                Le matricule peut être laissé vide : il sera généré automatiquement.
            </div>

            <form method="POST" action="{{ route('eleves.import.store') }}">
                @csrf
                <input type="hidden" name="classe" value="{{ $classe }}">
                <input type="hidden" name="annee_scolaire" value="{{ $anneeScolaire }}">

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-brand-sky">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Inclure</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Matricule</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Nom</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Prénoms</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($lignes as $i => $ligne)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <input type="hidden" name="lignes[{{ $i }}][inclure]" value="0">
                                            <input type="checkbox" name="lignes[{{ $i }}][inclure]" value="1" checked
                                                class="rounded border-gray-300 text-brand-sky focus:ring-brand-sky">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="lignes[{{ $i }}][matricule]" value="{{ $ligne['matricule'] }}"
                                                class="w-32 rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="lignes[{{ $i }}][nom]" value="{{ $ligne['nom'] }}"
                                                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="lignes[{{ $i }}][prenoms]" value="{{ $ligne['prenoms'] }}"
                                                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <a href="{{ route('eleves.import.form') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Recommencer</a>
                    <button type="submit" class="px-4 py-2 bg-brand-green border border-transparent rounded-md text-sm font-medium text-white hover:bg-brand-green-deep">
                        Enregistrer les élèves cochés
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
