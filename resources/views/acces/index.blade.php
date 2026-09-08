@use('Illuminate\Support\Facades\Gate')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-brand-sky-deep font-semibold">
            Gestion des accès
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('succes'))
                <div class="bg-brand-green/10 border border-brand-green text-brand-green-deep text-sm rounded-md px-4 py-3">
                    {{ session('succes') }}
                </div>
            @endif

            @if ($errors->has('acces'))
                <div class="bg-brand-salmon/10 border border-brand-salmon text-brand-salmon-deep text-sm rounded-md px-4 py-3">
                    {{ $errors->first('acces') }}
                </div>
            @endif

            {{-- Rappel des droits attachés à chaque rôle --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-serif text-lg text-brand-sky-deep font-semibold mb-4">Droits par rôle</h3>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="border border-gray-100 rounded-lg p-4">
                        <div class="font-semibold text-brand-ink">Développeur</div>
                        <p class="text-xs text-gray-500 mt-1">Accès technique complet</p>
                        <ul class="mt-3 space-y-1 text-sm text-gray-600 list-disc list-inside">
                            <li>Tout ce que fait le directeur général</li>
                            <li>Seul à pouvoir créer, modifier ou supprimer un compte développeur</li>
                        </ul>
                    </div>

                    <div class="border border-gray-100 rounded-lg p-4">
                        <div class="font-semibold text-brand-ink">Directeur général</div>
                        <p class="text-xs text-gray-500 mt-1">Accès complet à l'établissement</p>
                        <ul class="mt-3 space-y-1 text-sm text-gray-600 list-disc list-inside">
                            <li>Tout ce que fait la secrétaire</li>
                            <li>Supprimer un élève ou tous les élèves</li>
                            <li>Créer les accès et attribuer les rôles</li>
                        </ul>
                    </div>

                    <div class="border border-gray-100 rounded-lg p-4">
                        <div class="font-semibold text-brand-ink">Secrétaire</div>
                        <p class="text-xs text-gray-500 mt-1">Gestion quotidienne des élèves</p>
                        <ul class="mt-3 space-y-1 text-sm text-gray-600 list-disc list-inside">
                            <li>Ajouter et modifier un élève</li>
                            <li>Déplacer un élève de classe</li>
                            <li>Importer une liste PDF</li>
                            <li>Impression par lot</li>
                            <li>Signature et cachet de la carte</li>
                            <li class="text-brand-salmon-deep">Aucune suppression possible</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Création d'un accès --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-serif text-lg text-brand-sky-deep font-semibold mb-1">Créer un accès</h3>
                <p class="text-sm text-gray-600 mb-5">
                    La personne se connectera avec cet email et ce mot de passe. Elle pourra changer
                    son mot de passe depuis son profil.
                </p>

                <form method="POST" action="{{ route('acces.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nom complet" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Rôle" />
                        <select id="role" name="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-sky focus:ring-brand-sky">
                            @foreach ($roles as $cle => $libelle)
                                <option value="{{ $cle }}" @selected(old('role', \App\Models\User::ROLE_SECRETAIRE) === $cle)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <div class="sm:col-span-2 grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="password" value="Mot de passe" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-brand-sky text-white text-sm rounded-md hover:bg-brand-sky-deep">
                            Créer l'accès
                        </button>
                    </div>
                </form>
            </div>

            {{-- Comptes existants --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 pt-6">
                    <h3 class="font-serif text-lg text-brand-sky-deep font-semibold">
                        Accès existants ({{ $utilisateurs->count() }})
                    </h3>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-brand-sky">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nom</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Rôle</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($utilisateurs as $utilisateur)
                                <tr class="hover:bg-brand-sky/5">
                                    <td class="px-4 py-3 text-sm text-brand-ink font-medium">
                                        {{ $utilisateur->name }}
                                        @if ($utilisateur->is(auth()->user()))
                                            <span class="ml-1 text-xs text-gray-400">(vous)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $utilisateur->email }}</td>
                                    @php($modifiable = ! $utilisateur->estDeveloppeur() || Gate::allows('gerer-comptes-developpeur'))
                                    <td class="px-4 py-3 text-sm">
                                        @if ($modifiable)
                                            <form method="POST" action="{{ route('acces.update', $utilisateur) }}" class="inline-flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" onchange="this.form.submit()"
                                                    class="rounded-md border-gray-300 shadow-sm text-sm focus:border-brand-sky focus:ring-brand-sky">
                                                    @foreach ($roles as $cle => $libelle)
                                                        <option value="{{ $cle }}" @selected($utilisateur->role === $cle)>{{ $libelle }}</option>
                                                    @endforeach
                                                </select>
                                                <noscript>
                                                    <button type="submit" class="text-xs text-brand-sky-deep hover:underline">Appliquer</button>
                                                </noscript>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-sky/10 px-2.5 py-1 text-xs font-medium text-brand-sky-deep">
                                                {{ $utilisateur->libelleRole() }}
                                                <span class="text-gray-400" title="Modifiable uniquement par un développeur">🔒</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        @if ($utilisateur->is(auth()->user()) || ! $modifiable)
                                            <span class="text-xs text-gray-400">—</span>
                                        @else
                                            <form method="POST" action="{{ route('acces.destroy', $utilisateur) }}" class="inline"
                                                onsubmit="return confirm('Supprimer définitivement l\'accès de {{ $utilisateur->name }} ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-brand-salmon-deep hover:underline">Supprimer</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($errors->has('role'))
                    <div class="px-6 py-3 text-sm text-brand-salmon-deep">{{ $errors->first('role') }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
