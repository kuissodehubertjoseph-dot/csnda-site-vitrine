<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
                Élèves
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('cartes.lot.form') }}" class="inline-flex items-center px-4 py-2 bg-white border border-brand-green rounded-md text-sm font-medium text-brand-green-deep hover:bg-brand-green/10">
                    Impression par lot
                </a>
                <a href="{{ route('eleves.import.form') }}" class="inline-flex items-center px-4 py-2 bg-white border border-brand-sky rounded-md text-sm font-medium text-brand-sky-deep hover:bg-brand-sky/10">
                    Importer un PDF
                </a>
                @if ($eleves->total() > 0)
                    @can('supprimer-tous-eleves')
                    <form method="POST" action="{{ route('eleves.destroyTout') }}" onsubmit="return confirmerSuppressionTotale(this);">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="confirmation" value="">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-brand-salmon rounded-md text-sm font-medium text-brand-salmon-deep hover:bg-brand-salmon hover:text-white">
                            Supprimer tout
                        </button>
                    </form>
                    @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <script>
        function confirmerSuppressionTotale(form) {
            if (!confirm('Ceci va supprimer DÉFINITIVEMENT les {{ $eleves->total() }} élève(s) enregistré(s), avec leurs photos. Cette action est irréversible. Continuer ?')) {
                return false;
            }

            const saisie = prompt('Pour confirmer, tapez SUPPRIMER (en majuscules) :');

            if (saisie !== 'SUPPRIMER') {
                alert('Suppression annulée : le texte saisi ne correspond pas.');
                return false;
            }

            form.querySelector('input[name="confirmation"]').value = saisie;

            return true;
        }
    </script>

    <div
        class="py-8"
        x-data="{
            deplacementOuvert: false,
            eleveId: null,
            eleveNom: '',
            classeActuelle: '',
            ouvrirDeplacement(id, nom, classe) {
                this.eleveId = id;
                this.eleveNom = nom;
                this.classeActuelle = classe;
                this.deplacementOuvert = true;
            },
        }"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('succes'))
                <div class="bg-brand-green/10 border border-brand-green text-brand-green-deep text-sm rounded-md px-4 py-3">
                    {{ session('succes') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-4">
                <form method="GET" action="{{ route('eleves.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ $recherche }}"
                        placeholder="Rechercher par nom, prénoms ou matricule..."
                        class="sm:col-span-2 rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky"
                    >

                    <select name="classe" class="rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky">
                        <option value="">Toutes les classes</option>
                        @foreach ($classes as $classe)
                            <option value="{{ $classe }}" @selected($classeSelectionnee === $classe)>{{ $classe }}</option>
                        @endforeach
                    </select>

                    <select name="statut" class="rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky">
                        <option value="">Tous les statuts</option>
                        <option value="actif" @selected($statutSelectionne === 'actif')>Actif</option>
                        <option value="inactif" @selected($statutSelectionne === 'inactif')>Inactif</option>
                    </select>

                    <div class="sm:col-span-4 flex justify-end gap-2">
                        <a href="{{ route('eleves.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Réinitialiser</a>
                        <button type="submit" class="px-4 py-2 bg-brand-sky text-white text-sm rounded-md hover:bg-brand-sky-deep">Filtrer</button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-brand-sky">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matricule</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nom &amp; prénoms</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Classe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Âge</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Lieu de naissance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($eleves as $eleve)
                            <tr class="hover:bg-brand-sky/5">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-brand-sky-deep">{{ $eleve->matricule }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-brand-ink">
                                    <a href="{{ route('eleves.show', $eleve) }}" class="hover:underline font-medium">{{ $eleve->nom_complet }}</a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $eleve->classe }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $eleve->age }} ans</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $eleve->lieu_naissance ?: '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span @class([
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        'bg-brand-green/15 text-brand-green-deep' => $eleve->statut === 'actif',
                                        'bg-gray-200 text-gray-700' => $eleve->statut === 'inactif',
                                    ])>
                                        {{ ucfirst($eleve->statut) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm space-x-2">
                                    <a href="{{ route('eleves.show', $eleve) }}" class="text-brand-sky-deep hover:underline">Voir</a>
                                    <a href="{{ route('eleves.edit', $eleve) }}" class="text-brand-sky-deep hover:underline">Modifier</a>
                                    @can('deplacer-eleve')
                                        <button type="button"
                                            @click="ouvrirDeplacement({{ $eleve->id }}, @js($eleve->nom_complet), @js($eleve->classe))"
                                            class="text-brand-green-deep hover:underline">
                                            Déplacer
                                        </button>
                                    @endcan
                                    <form method="POST" action="{{ route('eleves.statut', $eleve) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-brand-salmon-deep hover:underline">
                                            {{ $eleve->statut === 'actif' ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                    @can('supprimer-eleve')
                                        <form method="POST" action="{{ route('eleves.destroy', $eleve) }}" class="inline" onsubmit="return confirm('Supprimer définitivement cet élève ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-brand-salmon-deep hover:underline">Supprimer</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">Aucun élève trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
              </div>
            </div>

            <div>
                {{ $eleves->links() }}
            </div>
        </div>

        {{-- Fenêtre de changement de classe --}}
        @can('deplacer-eleve')
            <div
                x-show="deplacementOuvert"
                x-cloak
                @keydown.escape.window="deplacementOuvert = false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                style="display: none;"
                role="dialog"
                aria-modal="true"
            >
                <div class="absolute inset-0 bg-black/50" @click="deplacementOuvert = false"></div>

                <div class="relative w-full max-w-sm rounded-lg bg-white p-6 shadow-xl">
                    <h3 class="font-serif text-lg font-semibold text-brand-sky-deep">Déplacer l'élève</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        <span class="font-medium text-brand-ink" x-text="eleveNom"></span>
                        — actuellement en <span class="font-medium" x-text="classeActuelle"></span>.
                    </p>

                    <form method="POST" :action="`{{ url('eleves') }}/${eleveId}/classe`" class="mt-5 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="nouvelle-classe" class="block text-sm font-medium text-gray-700">Nouvelle classe</label>
                            <select id="nouvelle-classe" name="classe" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky">
                                @foreach ($classes as $classeOption)
                                    <option value="{{ $classeOption }}" x-bind:selected="classeActuelle === @js($classeOption)">
                                        {{ $classeOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-1">
                            <button type="button" @click="deplacementOuvert = false"
                                class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-brand-green text-white text-sm font-medium rounded-md hover:bg-brand-green-deep">
                                Déplacer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>
</x-app-layout>
