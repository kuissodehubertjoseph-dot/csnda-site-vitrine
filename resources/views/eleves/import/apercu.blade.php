<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
            Vérifier les élèves détectés
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white shadow-sm rounded-lg p-4 text-sm text-gray-600">
                <strong>{{ count($lignes) }}</strong> ligne(s) détectée(s) dans le PDF.
                Vérifiez et corrigez les champs si besoin (matricule, sexe, date et lieu de naissance sont lus
                automatiquement), décochez les lignes à ignorer, puis validez.
                Le matricule doit provenir de la liste officielle : les lignes sans matricule ne seront pas
                enregistrées.
            </div>

            <form method="POST" action="{{ route('eleves.import.store') }}">
                @csrf

                <div class="bg-white shadow-sm rounded-lg p-4 mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="classe" value="Classe" />
                        <input id="classe" name="classe" type="text" list="classes-suggestions" required
                            value="{{ old('classe', $classe) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                        <datalist id="classes-suggestions">
                            @foreach ($classes as $classeOption)
                                <option value="{{ $classeOption }}">
                            @endforeach
                        </datalist>
                        @if ($classe === '')
                            <p class="mt-1 text-xs text-brand-salmon-deep">Non détectée dans le PDF : merci de la renseigner.</p>
                        @endif
                    </div>
                    <div>
                        <x-input-label for="annee_scolaire" value="Année scolaire" />
                        <x-text-input id="annee_scolaire" name="annee_scolaire" type="text" class="mt-1 block w-full text-sm"
                            :value="old('annee_scolaire', $anneeScolaire)" required />
                        @if ($anneeScolaire === '')
                            <p class="mt-1 text-xs text-brand-salmon-deep">Non détectée dans le PDF : merci de la renseigner.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-brand-sky">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Inclure</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Matricule</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Nom</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Prénom(s)</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Sexe</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Date de naissance</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Lieu de naissance</th>
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
                                                placeholder="Obligatoire"
                                                @class([
                                                    'w-32 rounded-md shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky',
                                                    'border-brand-salmon text-brand-salmon-deep' => $ligne['matricule'] === '',
                                                    'border-gray-300' => $ligne['matricule'] !== '',
                                                ])>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="lignes[{{ $i }}][nom]" value="{{ $ligne['nom'] }}"
                                                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="lignes[{{ $i }}][prenoms]" value="{{ $ligne['prenoms'] }}"
                                                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                                        </td>
                                        <td class="px-3 py-2">
                                            <select name="lignes[{{ $i }}][sexe]"
                                                class="w-20 rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                                                <option value="">—</option>
                                                <option value="M" @selected(($ligne['sexe'] ?? '') === 'M')>M</option>
                                                <option value="F" @selected(($ligne['sexe'] ?? '') === 'F')>F</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="date" name="lignes[{{ $i }}][date_naissance]" value="{{ $ligne['date_naissance'] ?? '' }}"
                                                class="w-36 rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="lignes[{{ $i }}][lieu_naissance]" value="{{ $ligne['lieu_naissance'] ?? '' }}"
                                                class="w-32 rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
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
