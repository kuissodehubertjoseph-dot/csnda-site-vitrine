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

        @if (request()->routeIs('ecoles.connexion'))
            <style>
                .carrousel-connexion {
                    position: fixed;
                    inset: 0;
                    z-index: 0;
                    overflow: hidden;
                    background: #0b1734;
                }

                .carrousel-connexion__slide {
                    position: absolute;
                    inset: 0;
                    background-size: cover;
                    background-position: center;
                    opacity: 0;
                    transform: scale(1.08);
                    animation: carrousel-fondu 21s infinite;
                }

                .carrousel-connexion__slide:nth-child(1) { animation-delay: 0s; }
                .carrousel-connexion__slide:nth-child(2) { animation-delay: 7s; }
                .carrousel-connexion__slide:nth-child(3) { animation-delay: 14s; }

                .carrousel-connexion::after {
                    content: '';
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(160deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.35) 45%, rgba(0, 0, 0, 0.6) 100%);
                }

                @keyframes carrousel-fondu {
                    0%    { opacity: 0; transform: scale(1.08); }
                    5%    { opacity: 1; transform: scale(1); }
                    28%   { opacity: 1; transform: scale(1); }
                    33%   { opacity: 0; transform: scale(1.02); }
                    100%  { opacity: 0; }
                }

                @media (prefers-reduced-motion: reduce) {
                    .carrousel-connexion__slide { animation: none; opacity: 0; }
                    .carrousel-connexion__slide:first-child { opacity: 1; transform: scale(1); }
                }

                #ecran-chargement {
                    position: fixed;
                    inset: 0;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    gap: 1rem;
                    background: linear-gradient(135deg, {{ config('ecole.slug') === 'ucao' ? '#E85D9C 0%, #C43B7A 100%' : '#5EB3E4 0%, #3E93C4 100%' }});
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
                    object-fit: contain;
                    background: #ffffff;
                    padding: 10px;
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
    <body class="font-sans text-brand-ink antialiased @if (request()->routeIs('ecoles.connexion')) chargement-actif @endif">
        @if (request()->routeIs('ecoles.connexion'))
            @php
                $photosCarrousel = [
                    'css' => [
                        'images/connexion/css-1.jpg',
                        'images/connexion/css-2.jpg',
                        'images/connexion/css-3.jpg',
                    ],
                    'jean-baptiste' => [
                        'images/connexion/jean-baptiste-1.jpg',
                        'images/connexion/jean-baptiste-2.jpg',
                        'images/connexion/jean-baptiste-3.jpg',
                    ],
                    'ucao' => [
                        'images/connexion/ucao-1.jpg',
                        'images/connexion/ucao-2.jpg',
                        'images/connexion/ucao-3.jpg',
                    ],
                ][config('ecole.slug')] ?? [];
            @endphp

            @if (count($photosCarrousel))
                <div class="carrousel-connexion" aria-hidden="true">
                    @foreach ($photosCarrousel as $photo)
                        <div class="carrousel-connexion__slide" style="background-image: url('{{ asset($photo) }}')"></div>
                    @endforeach
                </div>
            @endif

            <div id="ecran-chargement">
                <div class="anneau-chargement">
                    <img src="{{ asset(config('cortex.logo')) }}" alt="Logo {{ config('cortex.nom') }}">
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

        <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 @if (empty($photosCarrousel ?? [])) bg-gradient-to-br from-brand-sky to-brand-sky-deep @endif">
            <div class="relative z-10 flex flex-col items-center gap-3">
                <a href="/" class="flex h-24 w-24 items-center justify-center rounded-full bg-white p-1 shadow-lg">
                    <img src="{{ asset(config('ecole.logo')) }}" alt="Logo {{ config('ecole.sigle') }}" class="h-full w-full rounded-full object-cover">
                </a>
                <span class="text-white font-serif text-lg tracking-wide drop-shadow">{{ config('ecole.nom') }}</span>
            </div>

            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
