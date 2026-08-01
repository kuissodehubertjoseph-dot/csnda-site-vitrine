<x-app-layout>
    <x-slot name="header">
        <h1 class="font-extrabold text-xl text-encre uppercase tracking-wide">Coordonnées &amp; contact</h1>
    </x-slot>

    <div class="bg-white rounded-2xl shadow p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="accueil_accroche" value="Phrase d'accroche (page d'accueil)" />
                <textarea id="accueil_accroche" name="accueil_accroche" rows="3" class="mt-1 w-full rounded-md border-ciel/30 focus:border-ciel focus:ring-ciel">{{ old('accueil_accroche', $settings['accueil_accroche']) }}</textarea>
                <x-input-error :messages="$errors->get('accueil_accroche')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_telephone" value="Téléphone" />
                <x-text-input id="contact_telephone" name="contact_telephone" type="text" class="mt-1 w-full" value="{{ old('contact_telephone', $settings['contact_telephone']) }}" />
                <x-input-error :messages="$errors->get('contact_telephone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_email" value="Email de contact (reçoit les messages du formulaire)" />
                <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 w-full" value="{{ old('contact_email', $settings['contact_email']) }}" />
                <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_adresse" value="Adresse" />
                <textarea id="contact_adresse" name="contact_adresse" rows="2" class="mt-1 w-full rounded-md border-ciel/30 focus:border-ciel focus:ring-ciel">{{ old('contact_adresse', $settings['contact_adresse']) }}</textarea>
                <x-input-error :messages="$errors->get('contact_adresse')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_map_embed" value="Code d'intégration Google Maps (optionnel, iframe complet)" />
                <textarea id="contact_map_embed" name="contact_map_embed" rows="3" placeholder="<iframe src=... ></iframe>" class="mt-1 w-full rounded-md border-ciel/30 focus:border-ciel focus:ring-ciel font-mono text-xs">{{ old('contact_map_embed', $settings['contact_map_embed']) }}</textarea>
                <p class="text-xs text-encre/50 mt-1">Laissez vide pour utiliser automatiquement l'adresse ci-dessus sur Google Maps.</p>
                <x-input-error :messages="$errors->get('contact_map_embed')" class="mt-2" />
            </div>

            <x-primary-button>Enregistrer</x-primary-button>
        </form>
    </div>
</x-app-layout>
