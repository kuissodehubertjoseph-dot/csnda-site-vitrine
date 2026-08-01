<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }} - Back-office</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-encre bg-ciel/5">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col min-w-0">
                <header class="bg-white shadow-sm lg:hidden flex items-center justify-between px-4 h-16">
                    <button @click="sidebarOpen = true" class="text-ciel">
                        <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="font-extrabold text-ciel">CSNDA — Admin</span>
                    <span class="w-7"></span>
                </header>

                @isset($header)
                    <div class="bg-white shadow-sm">
                        <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="flex-1">
                    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                        @if (session('success'))
                            <div class="mb-6 bg-laurier/10 border-2 border-laurier text-laurier rounded-lg p-4 text-sm font-semibold">
                                {{ session('success') }}
                            </div>
                        @endif
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
