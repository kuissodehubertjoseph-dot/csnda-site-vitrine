<x-layouts.public :title="'Contact'">

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-encre uppercase tracking-wide text-center mb-10">
            Contact &amp; localisation
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                @if (session('status'))
                    <div class="mb-6 bg-laurier/10 border-2 border-laurier text-laurier rounded-lg p-4 text-sm font-semibold">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-5 bg-white border-2 border-ciel/20 rounded-2xl p-6 shadow">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-bold text-encre mb-1">Nom complet</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                               class="w-full rounded-lg border-ciel/30 focus:border-ciel focus:ring-ciel">
                        @error('name') <p class="text-saumon text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-bold text-encre mb-1">Adresse email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               class="w-full rounded-lg border-ciel/30 focus:border-ciel focus:ring-ciel">
                        @error('email') <p class="text-saumon text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-bold text-encre mb-1">Message</label>
                        <textarea id="message" name="message" rows="5" required
                                  class="w-full rounded-lg border-ciel/30 focus:border-ciel focus:ring-ciel">{{ old('message') }}</textarea>
                        @error('message') <p class="text-saumon text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full bg-laurier text-white font-bold uppercase text-sm px-8 py-3 rounded-full hover:bg-laurier/90 transition">
                        Envoyer le message
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="bg-white border-2 border-ciel/20 rounded-2xl p-6 shadow space-y-4">
                    <div>
                        <h2 class="font-bold text-ciel uppercase text-sm tracking-wide mb-1">Adresse</h2>
                        <p class="text-encre/80">{{ $adresse }}</p>
                    </div>
                    <div>
                        <h2 class="font-bold text-ciel uppercase text-sm tracking-wide mb-1">Téléphone</h2>
                        <p class="text-encre/80">{{ $telephone }}</p>
                    </div>
                    <div>
                        <h2 class="font-bold text-ciel uppercase text-sm tracking-wide mb-1">Email</h2>
                        <p class="text-encre/80">{{ $email }}</p>
                    </div>
                </div>

                <div class="rounded-2xl overflow-hidden border-2 border-ciel/20 shadow h-64 sm:h-80">
                    @if ($mapEmbed)
                        {!! $mapEmbed !!}
                    @else
                        <iframe
                            src="https://www.google.com/maps?q={{ urlencode($adresse) }}&output=embed"
                            class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    @endif
                </div>
            </div>
        </div>
    </section>

</x-layouts.public>
