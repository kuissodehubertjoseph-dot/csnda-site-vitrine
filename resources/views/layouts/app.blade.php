<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|cormorant-garamond:500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (in_array(config('ecole.slug'), ['ucao', 'egei'], true))
            {{-- Recolore en rose toute l'interface authentifiée (boutons, liens,
            nav, focus...) qui utilise les classes Tailwind brand-sky / brand-sky-deep
            — voir resources/css/app.css pour les valeurs par défaut (bleu marine CSS). --}}
            <style>
                :root {
                    --brand-sky: 224 80 122;
                    --brand-sky-deep: 194 55 100;
                }
            </style>
        @elseif (config('ecole.slug') === 'jean-baptiste')
            {{-- Recolore en vert toute l'interface authentifiée (boutons, liens,
            nav, focus...) — même mécanisme que UCAO ci-dessus, valeurs reprises
            de brand-green / brand-green-deep (tailwind.config.js). --}}
            <style>
                :root {
                    --brand-sky: 76 175 109;
                    --brand-sky-deep: 55 146 88;
                }
            </style>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="relative min-h-screen isolate">
            <!-- Arrière-plan : logo de l'école agrandi et flouté, effet verre dépoli -->
            <div class="fixed inset-0 -z-10 overflow-hidden bg-gray-50">
                <div
                    class="absolute inset-0 bg-center bg-cover scale-125"
                    style="background-image: url('{{ asset(config('ecole.logo')) }}'); filter: blur(48px) saturate(1.15);"
                ></div>
                <div class="absolute inset-0 bg-gradient-to-br from-brand-sky/20 via-white/70 to-brand-salmon/10"></div>
            </div>

            <div class="bg-white/70 backdrop-blur-md shadow-sm">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="border-t border-white/60">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
            </div>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
