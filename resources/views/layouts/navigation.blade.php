@php
    $adminLinks = [
        'admin.dashboard' => ['label' => 'Tableau de bord', 'route' => 'admin.dashboard'],
        'admin.pages.edit' => ['label' => 'Contenu des pages', 'route' => ['admin.pages.edit', 'presentation']],
        'admin.actualites.index' => ['label' => 'Actualités', 'route' => 'admin.actualites.index'],
        'admin.filieres.index' => ['label' => 'Filières & classes', 'route' => 'admin.filieres.index'],
        'admin.categories-galerie.index' => ['label' => 'Catégories galerie', 'route' => 'admin.categories-galerie.index'],
        'admin.photos-galerie.index' => ['label' => 'Photos galerie', 'route' => 'admin.photos-galerie.index'],
        'admin.settings.edit' => ['label' => 'Coordonnées & contact', 'route' => 'admin.settings.edit'],
        'admin.messages.index' => ['label' => 'Messages reçus', 'route' => 'admin.messages.index'],
    ];
@endphp

<!-- Overlay mobile -->
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-encre/50 z-30 lg:hidden"></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 w-72 bg-encre text-white transform transition-transform duration-200 lg:static lg:translate-x-0 flex flex-col">

    <div class="flex items-center gap-3 px-6 h-20 border-b border-white/10">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo CSNDA" class="h-12 w-12 bg-white rounded-full p-1">
        <div class="leading-tight">
            <p class="font-extrabold text-ciel text-sm uppercase">CSNDA</p>
            <p class="text-xs text-white/60">Espace administrateur</p>
        </div>
        <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-white/70">✕</button>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1 text-sm">
        @foreach ($adminLinks as $key => $link)
            @php $routeName = is_array($link['route']) ? $link['route'][0] : $link['route']; @endphp
            <a href="{{ is_array($link['route']) ? route(...$link['route']) : route($link['route']) }}"
               class="block px-4 py-2.5 rounded-lg font-semibold transition {{ request()->routeIs($routeName) || request()->routeIs(str($routeName)->before('.edit').'*') ? 'bg-ciel text-white' : 'text-white/80 hover:bg-white/10' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-4">
        <a href="{{ route('home') }}" target="_blank" class="block text-xs text-laurier hover:underline mb-3">Voir le site public &rarr;</a>
        <div class="flex items-center justify-between">
            <div class="text-xs">
                <p class="font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-white/60">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <div class="mt-3 flex gap-2">
            <a href="{{ route('profile.edit') }}" class="flex-1 text-center text-xs bg-white/10 hover:bg-white/20 rounded-lg py-2">Profil</a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full text-center text-xs bg-saumon/80 hover:bg-saumon rounded-lg py-2">Déconnexion</button>
            </form>
        </div>
    </div>
</aside>
