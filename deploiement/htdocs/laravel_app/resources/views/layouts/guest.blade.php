<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (request()->routeIs('login'))
            <style>
                #ecran-chargement {
                    position: fixed;
                    inset: 0;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    gap: 1rem;
                    background: linear-gradient(135deg, #5EB3E4 0%, #3E93C4 100%);
                    transition: opacity 0.5s ease, visibility 0.5s ease;
                }

                #ecran-chargement.masque {
                    opacity: 0;
                    visibility: hidden;
                    pointer-events: none;
                }

                .anneau-chargement {
                    position: relative;
                    width: 6.5rem;
                    height: 6.5rem;
                }

                .anneau-chargement::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    border-radius: 9999px;
                    border: 4px solid rgba(255, 255, 255, 0.3);
                    border-top-color: #ffffff;
                    animation: tourner 0.9s linear infinite;
                }

                .anneau-chargement img {
                    position: absolute;
                    inset: 8px;
                    width: calc(100% - 16px);
                    height: calc(100% - 16px);
                    border-radius: 9999px;
                    object-fit: cover;
                    background: #ffffff;
                    padding: 3px;
                }

                @keyframes tourner {
                    to { transform: rotate(360deg); }
                }

                body.chargement-actif {
                    overflow: hidden;
                }
            </style>
        @endif
    </head>
    <body class="font-sans text-brand-ink antialiased @if (request()->routeIs('login')) chargement-actif @endif">
        @if (request()->routeIs('login'))
            <div id="ecran-chargement">
                <div class="anneau-chargement">
                    <img src="{{ asset(config('ecole.logo')) }}" alt="Logo {{ config('ecole.sigle') }}">
                </div>
                <span class="text-white font-serif text-sm tracking-widest uppercase">Chargement…</span>
            </div>
            <script>
                window.addEventListener('load', function () {
                    setTimeout(function () {
                        var ecran = document.getElementById('ecran-chargement');
                        ecran.classList.add('masque');
                        document.body.classList.remove('chargement-actif');
                        ecran.addEventListener('transitionend', function () { ecran.remove(); }, { once: true });
                    }, 2200);
                });
            </script>
        @endif

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-brand-sky to-brand-sky-deep">
            <div class="flex flex-col items-center gap-3">
                <a href="/" class="flex h-24 w-24 items-center justify-center rounded-full bg-white p-1 shadow-lg">
                    <img src="{{ asset(config('ecole.logo')) }}" alt="Logo {{ config('ecole.sigle') }}" class="h-full w-full rounded-full object-cover">
                </a>
                <span class="text-white font-serif text-lg tracking-wide">{{ config('ecole.nom') }}</span>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
