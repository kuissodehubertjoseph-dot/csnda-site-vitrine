<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Connexion - {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-encre antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-ciel/10">
            <a href="{{ route('home') }}" class="flex flex-col items-center">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo CSNDA" class="w-24 h-24">
                <span class="mt-2 font-extrabold text-ciel uppercase text-sm tracking-wide">Espace administrateur</span>
            </a>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-2xl border-2 border-ciel/20">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
