@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }} - CSNDA Cotonou</title>
    <meta name="description" content="{{ $description ?? 'Cours Secondaire Notre-Dame des Apôtres de Cotonou, Bénin - Prière, Discipline, Travail.' }}">

    <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-encre bg-white flex flex-col min-h-screen">

    <header x-data="{ mobileOpen: false }" class="bg-white border-b-4 border-ciel sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo CSNDA" class="h-14 w-14">
                    <span class="leading-tight hidden sm:block">
                        <span class="block font-extrabold text-ciel text-sm md:text-base uppercase tracking-wide">Cours Secondaire</span>
                        <span class="block font-extrabold text-laurier text-sm md:text-base uppercase tracking-wide">Notre-Dame des Apôtres</span>
                    </span>
                </a>

                <nav class="hidden lg:flex items-center gap-6 text-sm font-semibold uppercase tracking-wide">
                    @php
                        $links = [
                            'home' => 'Accueil',
                            'presentation' => 'Présentation',
                            'filieres' => 'Filières',
                            'news.index' => 'Actualités',
                            'gallery' => 'Galerie',
                            'contact' => 'Contact',
                        ];
                    @endphp
                    @foreach ($links as $route => $label)
                        <a href="{{ route($route) }}"
                           class="pb-1 border-b-2 {{ request()->routeIs($route.'*') ? 'border-ciel text-ciel' : 'border-transparent text-encre hover:text-ciel hover:border-ciel' }} transition">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                <a href="{{ route('contact') }}" class="hidden lg:inline-block bg-laurier text-white text-sm font-bold uppercase px-5 py-2.5 rounded-full hover:bg-laurier/90 transition">
                    Nous contacter
                </a>

                <button @click="mobileOpen = !mobileOpen" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-ciel">
                    <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak class="lg:hidden border-t border-ciel/20 bg-white">
            <nav class="px-4 py-3 space-y-1 text-sm font-semibold uppercase tracking-wide">
                @foreach ($links as $route => $label)
                    <a href="{{ route($route) }}"
                       class="block py-2 {{ request()->routeIs($route.'*') ? 'text-ciel' : 'text-encre hover:text-ciel' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-encre text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo CSNDA" class="h-14 w-14 bg-white rounded-full p-1">
                    <span class="font-extrabold text-ciel">CSNDA</span>
                </div>
                <p class="text-sm text-white/80">Cours Secondaire Notre-Dame des Apôtres</p>
                <p class="text-sm italic text-saumon mt-2">"Optimus esse aut non esse"</p>
            </div>

            <div>
                <h3 class="font-bold text-laurier uppercase text-sm tracking-wide mb-4">Navigation</h3>
                <ul class="space-y-2 text-sm text-white/80">
                    <li><a href="{{ route('presentation') }}" class="hover:text-ciel">Présentation</a></li>
                    <li><a href="{{ route('filieres') }}" class="hover:text-ciel">Filières &amp; classes</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-ciel">Actualités</a></li>
                    <li><a href="{{ route('gallery') }}" class="hover:text-ciel">Galerie photos</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-laurier uppercase text-sm tracking-wide mb-4">Contact</h3>
                <ul class="space-y-2 text-sm text-white/80">
                    <li>{{ \App\Models\Setting::get('contact_adresse', 'Cotonou, Bénin') }}</li>
                    <li>{{ \App\Models\Setting::get('contact_telephone', '+229 00 00 00 00') }}</li>
                    <li>{{ \App\Models\Setting::get('contact_email', 'contact@csnda-cotonou.bj') }}</li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-laurier uppercase text-sm tracking-wide mb-4">Devise</h3>
                <p class="text-sm text-white/80">Prière • Discipline • Travail</p>
                <p class="text-sm text-white/80 mt-2">Dignité Féminine</p>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs text-white/60">
            &copy; {{ date('Y') }} Cours Secondaire Notre-Dame des Apôtres — Cotonou, Bénin. Tous droits réservés.
        </div>
    </footer>

    @vite('resources/js/app.js')
</body>
</html>
