<nav x-data="{ open: false }" class="bg-gradient-to-r from-brand-sky to-brand-sky-deep border-b border-brand-salmon">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative flex items-center min-h-16 py-2">
            <!-- Logo -->
            <div class="shrink-0 flex items-center gap-3 -ml-4 sm:-ml-8">
                <a href="{{ route('eleves.index') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white p-0.5 shadow shrink-0">
                        <img src="{{ asset(config('ecole.logo')) }}" alt="Logo {{ config('ecole.sigle') }}" class="h-full w-full rounded-full object-cover">
                    </span>
                    <span class="text-white font-serif text-sm leading-tight tracking-wide hidden sm:inline max-w-xs">{{ config('ecole.nom') }}</span>
                </a>
            </div>

            <!-- Navigation Links (centrés au milieu de la barre) -->
            <div class="hidden sm:flex absolute left-1/2 -translate-x-1/2 space-x-8">
                <x-nav-link :href="route('eleves.index')" :active="request()->routeIs('eleves.*')" class="text-white">
                    {{ __('Élèves') }}
                </x-nav-link>
                <x-nav-link :href="route('cartes.lot.form')" :active="request()->routeIs('cartes.lot.*')" class="text-white">
                    {{ __('Impression par lot') }}
                </x-nav-link>
            </div>

            <div class="ms-auto"></div>

            <!-- Ajout rapide d'un élève -->
            <div class="hidden sm:flex sm:items-center shrink-0">
                <a href="{{ route('eleves.create') }}"
                    class="inline-flex items-center gap-2 rounded-md bg-white px-3.5 py-2 text-sm font-semibold text-brand-sky-deep shadow-sm transition hover:bg-white/90">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                    {{ __('Ajouter un élève') }}
                </a>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-4 shrink-0">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <div class="text-xs text-gray-500">Connecté en tant que</div>
                            <div class="text-sm font-semibold text-brand-ink">{{ Auth::user()->libelleRole() }}</div>
                        </div>

                        @can('gerer-acces')
                            <x-dropdown-link :href="route('acces.index')">
                                {{ __('Gestion des accès') }}
                            </x-dropdown-link>
                        @endcan

                        @can('gerer-parametres-carte')
                            <x-dropdown-link :href="route('parametres.edit')">
                                {{ __('Paramètres de la carte') }}
                            </x-dropdown-link>
                        @endcan

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 ms-auto flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('eleves.index')" :active="request()->routeIs('eleves.index')">
                {{ __('Élèves') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('eleves.create')" :active="request()->routeIs('eleves.create')">
                {{ __('Ajouter un élève') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cartes.lot.form')" :active="request()->routeIs('cartes.lot.*')">
                {{ __('Impression par lot') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/20">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-white/80">{{ Auth::user()->email }}</div>
                <div class="mt-1 inline-block rounded-full bg-white/15 px-2 py-0.5 text-xs font-medium text-white">
                    {{ Auth::user()->libelleRole() }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                @can('gerer-acces')
                    <x-responsive-nav-link :href="route('acces.index')">
                        {{ __('Gestion des accès') }}
                    </x-responsive-nav-link>
                @endcan

                @can('gerer-parametres-carte')
                    <x-responsive-nav-link :href="route('parametres.edit')">
                        {{ __('Paramètres de la carte') }}
                    </x-responsive-nav-link>
                @endcan

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
