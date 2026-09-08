<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('cortex.nom') }} — Cartes scolaires sécurisées pour établissements</title>
    <meta name="description" content="CORTEX BÉNIN TV accompagne les établissements scolaires dans la création, la gestion et l'impression sécurisée de leurs cartes scolaires.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Révélation au scroll — pilotée par l'Intersection Observer en bas de page. */
        .reveler {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 700ms cubic-bezier(0.16, 1, 0.3, 1),
                        transform 700ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveler.visible {
            opacity: 1;
            transform: none;
        }

        /* Reflet diagonal qui traverse le bouton principal au survol. */
        .cta-brillance::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(115deg, transparent 30%, rgba(255, 255, 255, 0.28) 50%, transparent 70%);
            transform: translateX(-120%);
            transition: transform 700ms cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }

        .cta-brillance:hover::after {
            transform: translateX(120%);
        }

        /* Timeline de la section « Comment ça fonctionne » : le trait se dessine au scroll. */
        .trait-timeline {
            stroke-dasharray: 1;
            stroke-dashoffset: 1;
            transition: stroke-dashoffset 1400ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .trait-timeline.visible {
            stroke-dashoffset: 0;
        }

        @media (prefers-reduced-motion: reduce) {
            .reveler,
            .trait-timeline {
                opacity: 1 !important;
                transform: none !important;
                stroke-dashoffset: 0 !important;
                transition: none !important;
            }

            .cta-brillance::after {
                display: none;
            }

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>

    {{-- Sans JavaScript, l'observateur ne peut pas révéler le contenu : on le rend visible d'office. --}}
    <noscript>
        <style>
            .reveler { opacity: 1 !important; transform: none !important; }
            .trait-timeline { stroke-dashoffset: 0 !important; }
        </style>
    </noscript>
</head>
<body
    class="font-cortex antialiased text-cortex-body bg-white"
    x-data="{ connexionOuverte: {{ $errors->any() ? 'true' : 'false' }}, defile: false }"
    @scroll.window="defile = window.scrollY > 40"
>
    @if (session('erreur'))
        <div class="fixed inset-x-0 top-0 z-50 bg-cortex-rouge px-5 py-3 text-center text-sm font-semibold text-white shadow-md">
            {{ session('erreur') }}
        </div>
    @endif

    {{-- ==================== 1. HEADER ==================== --}}
    <header
        class="fixed inset-x-0 top-0 z-40 transition-all duration-300 ease-rapide"
        :class="defile ? 'bg-white/85 backdrop-blur-xl shadow-[0_1px_2px_rgba(17,17,20,0.04),0_8px_24px_-12px_rgba(17,17,20,0.12)]' : 'bg-transparent'"
    >
        <div class="mx-auto max-w-[1280px] px-5 sm:px-8">
            <div class="flex h-[72px] items-center justify-between">
                <a href="{{ route('accueil') }}" class="flex shrink-0 items-center gap-2.5">
                    <img src="{{ asset(config('cortex.logo')) }}" alt="Logo {{ config('cortex.nom') }}" class="h-9 w-auto object-contain sm:h-10">
                    <span class="whitespace-nowrap text-sm font-bold tracking-tight text-cortex-encre sm:text-[15px]">
                        CORTEX <span class="text-cortex-rouge">BÉNIN TV</span>
                    </span>
                </a>

                <button
                    @click="connexionOuverte = true"
                    class="cta-brillance relative overflow-hidden inline-flex items-center gap-2 rounded-xl bg-cortex-rouge px-4 sm:px-5 py-2.5 text-[13px] sm:text-sm font-semibold text-white
                           shadow-[0_1px_2px_rgba(229,57,53,0.24),0_8px_20px_-8px_rgba(229,57,53,0.5)]
                           transition-all duration-200 ease-rapide
                           hover:bg-cortex-rouge-deep hover:scale-[1.03] hover:shadow-[0_2px_4px_rgba(229,57,53,0.3),0_14px_30px_-10px_rgba(229,57,53,0.65)]
                           focus:outline-none focus-visible:ring-2 focus-visible:ring-cortex-rouge focus-visible:ring-offset-2"
                >
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" />
                    </svg>
                    <span class="hidden whitespace-nowrap lg:inline">Accéder à l'espace établissement</span>
                    <span class="whitespace-nowrap lg:hidden">Connexion</span>
                </button>
            </div>
        </div>
    </header>

    {{-- ==================== 2. HERO ==================== --}}
    <section class="relative overflow-hidden pt-[128px] pb-20 sm:pt-[152px] sm:pb-28">
        {{-- Formes rouges décoratives, flottement lent --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-24 right-[-8%] h-[420px] w-[420px] rounded-full bg-cortex-rouge/[0.07] blur-3xl animate-flotter"></div>
            <div class="absolute top-[38%] left-[-10%] h-[320px] w-[320px] rounded-full bg-cortex-rouge/[0.05] blur-3xl animate-flotter-lent"></div>
        </div>

        <div class="mx-auto max-w-[1280px] px-5 sm:px-8">
            <div class="grid items-center gap-14 lg:grid-cols-12 lg:gap-12">

                {{-- Colonne gauche --}}
                <div class="lg:col-span-6">
                    <h1 class="reveler mt-6 text-[38px] leading-[1.1] sm:text-[52px] xl:text-[58px] font-bold tracking-[-0.03em] text-cortex-encre" style="transition-delay: 80ms;">
                        Simplifiez la gestion de vos établissements.
                        <span class="block text-cortex-rouge">Valorisez vos élèves.</span>
                    </h1>

                    <p class="reveler mt-6 max-w-xl text-base sm:text-lg leading-relaxed text-cortex-body" style="transition-delay: 160ms;">
                        {{ config('cortex.nom') }} accompagne les établissements scolaires dans la création,
                        la gestion et l'impression sécurisée de leurs cartes scolaires, rapidement et sans complexité.
                    </p>

                    <div class="reveler mt-9 flex flex-col items-start gap-4" style="transition-delay: 240ms;">
                        <button
                            @click="connexionOuverte = true"
                            class="cta-brillance group relative overflow-hidden inline-flex items-center gap-2.5 rounded-xl bg-cortex-rouge px-7 py-4 text-[15px] font-semibold text-white
                                   shadow-[0_2px_4px_rgba(229,57,53,0.24),0_16px_36px_-12px_rgba(229,57,53,0.55)]
                                   transition-all duration-200 ease-rapide
                                   hover:bg-cortex-rouge-deep hover:scale-[1.03] hover:shadow-[0_4px_8px_rgba(229,57,53,0.3),0_22px_48px_-14px_rgba(229,57,53,0.7)]
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-cortex-rouge focus-visible:ring-offset-2"
                        >
                            Accéder à l'espace établissement
                            <svg class="h-4 w-4 transition-transform duration-200 ease-rapide group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Colonne droite --}}
                <div class="lg:col-span-6">
                    <div
                        class="reveler relative"
                        style="transition-delay: 320ms; transition-duration: 900ms;"
                        data-reveler-scale
                    >
                        <div aria-hidden="true" class="absolute -inset-6 -z-10 rounded-[36px] bg-gradient-to-br from-cortex-rouge/[0.10] via-cortex-rouge/[0.03] to-transparent blur-2xl"></div>
                        <img
                            src="{{ asset('images/4628.png') }}"
                            alt="Carte d'identité scolaire recto-verso réalisée par {{ config('cortex.nom') }}"
                            class="w-full rounded-[20px] object-cover shadow-[0_2px_8px_rgba(17,17,20,0.04),0_24px_60px_-20px_rgba(17,17,20,0.28)]"
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 5. ÉTABLISSEMENTS ==================== --}}
    <section id="etablissements" class="scroll-mt-24 bg-cortex-surface-2 py-20 sm:py-28">
        <div class="mx-auto max-w-[1280px] px-5 sm:px-8">
            <div class="mt-14 mx-auto flex max-w-3xl flex-wrap justify-center gap-5">
                @foreach ($ecoles as $i => $ecole)
                    {{-- Une entrée est soit un simple nom, soit ['nom' => ..., 'logo' => ..., 'slug' => ..., 'statut' => 'actif'|'bientot']. --}}
                    @php($nomEcole = trim(is_array($ecole) ? $ecole['nom'] : $ecole))
                    @php($logoEcole = is_array($ecole) ? ($ecole['logo'] ?? null) : null)
                    @php($slugEcole = is_array($ecole) ? ($ecole['slug'] ?? null) : null)
                    @php($estActive = $slugEcole && (($ecole['statut'] ?? null) === 'actif'))
                    <div
                        class="reveler group flex w-full max-w-[220px] flex-1 basis-[180px] flex-col items-center gap-4 rounded-2xl border border-cortex-border bg-white p-6 text-center
                               shadow-[0_1px_2px_rgba(17,17,20,0.03),0_8px_24px_-16px_rgba(17,17,20,0.12)]
                               transition-all duration-300 ease-rapide
                               hover:-translate-y-1 hover:border-cortex-rouge/30
                               hover:shadow-[0_2px_6px_rgba(229,57,53,0.06),0_18px_40px_-20px_rgba(229,57,53,0.3)]"
                        style="transition-delay: {{ ($i % 4) * 70 }}ms;"
                    >
                        @if ($logoEcole)
                            <span class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-white ring-1 ring-cortex-border">
                                <img src="{{ asset($logoEcole) }}" alt="Logo {{ $nomEcole }}" class="h-full w-full object-contain">
                            </span>
                        @else
                            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cortex-rouge/[0.07] text-cortex-rouge transition-colors duration-300 ease-rapide group-hover:bg-cortex-rouge/[0.13]">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M5 21V9.5L12 4l7 5.5V21M9.5 21v-5.5h5V21M9.5 12h.01M14.5 12h.01" />
                                </svg>
                            </span>
                        @endif
                        <span class="text-[14px] font-semibold leading-snug text-cortex-encre">{{ $nomEcole }}</span>

                        @if ($estActive)
                            <a href="{{ route('ecoles.connexion', $slugEcole) }}"
                               class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-cortex-rouge/[0.08] px-3.5 py-1.5 text-[12.5px] font-semibold text-cortex-rouge transition-colors duration-200 ease-rapide hover:bg-cortex-rouge hover:text-white">
                                Se connecter
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" />
                                </svg>
                            </a>
                        @else
                            <span class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-cortex-surface-2 px-3.5 py-1.5 text-[12.5px] font-semibold text-cortex-body">
                                Bientôt disponible
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== 7. FOOTER ==================== --}}
    <footer class="border-t border-cortex-border bg-cortex-surface">
        <div class="mx-auto max-w-[1280px] px-5 sm:px-8 py-10">
            <div class="flex flex-col items-center gap-6 sm:flex-row sm:justify-between">
                <a href="{{ route('accueil') }}" class="flex items-center gap-3">
                    <img src="{{ asset(config('cortex.logo')) }}" alt="Logo {{ config('cortex.nom') }}" class="h-9 w-auto object-contain">
                    <span class="text-sm font-bold tracking-tight text-cortex-encre">
                        CORTEX <span class="text-cortex-rouge">BÉNIN TV</span>
                    </span>
                </a>

                <nav class="flex flex-wrap items-center justify-center gap-x-7 gap-y-2 text-sm text-cortex-body">
                    <a href="#etablissements" class="transition-colors duration-200 ease-rapide hover:text-cortex-rouge">Établissements</a>
                </nav>
            </div>

            <p class="mt-8 border-t border-cortex-border pt-6 text-center text-[13px] text-cortex-body/70">
                © {{ date('Y') }} {{ config('cortex.nom') }}, Tous droits réservés.
            </p>
        </div>
    </footer>

    {{-- ==================== PANNEAU DE CONNEXION ==================== --}}
    <div
        x-show="connexionOuverte"
        x-cloak
        @keydown.escape.window="connexionOuverte = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titre-connexion"
    >
        <div
            x-show="connexionOuverte"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-cortex-encre/50 backdrop-blur-sm"
            @click="connexionOuverte = false"
        ></div>

        <div
            x-show="connexionOuverte"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-[0.98]"
            class="relative w-full max-w-[400px] rounded-[20px] bg-white p-8 shadow-[0_4px_12px_rgba(17,17,20,0.08),0_32px_80px_-24px_rgba(17,17,20,0.4)]"
        >
            <button
                @click="connexionOuverte = false"
                class="absolute right-4 top-4 rounded-lg p-1.5 text-cortex-body/50 transition-colors duration-200 ease-rapide hover:bg-cortex-surface-2 hover:text-cortex-encre"
                aria-label="Fermer"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="text-center">
                <img src="{{ asset(config('cortex.logo')) }}" alt="" class="mx-auto h-12 w-auto object-contain">
                <h2 id="titre-connexion" class="mt-4 text-lg font-bold tracking-[-0.01em] text-cortex-encre">Espace établissement</h2>
                <p class="mt-1.5 text-[13.5px] text-cortex-body">Choisissez votre établissement pour accéder à votre tableau de bord.</p>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-cortex-rouge/20 bg-cortex-rouge/[0.05] px-4 py-3 text-[13.5px] text-cortex-rouge-deep">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mt-7 space-y-2.5">
                @foreach ($ecoles as $ecole)
                    @php($nomEcole = trim(is_array($ecole) ? $ecole['nom'] : $ecole))
                    @php($logoEcole = is_array($ecole) ? ($ecole['logo'] ?? null) : null)
                    @php($slugEcole = is_array($ecole) ? ($ecole['slug'] ?? null) : null)
                    @php($estActive = $slugEcole && (($ecole['statut'] ?? null) === 'actif'))

                    @if ($estActive)
                        <a href="{{ route('ecoles.connexion', $slugEcole) }}"
                           class="group flex items-center gap-3 rounded-xl border border-cortex-border bg-white px-4 py-3 text-left transition-all duration-200 ease-rapide hover:border-cortex-rouge/30 hover:bg-cortex-rouge/[0.04]">
                            @if ($logoEcole)
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white ring-1 ring-cortex-border">
                                    <img src="{{ asset($logoEcole) }}" alt="Logo {{ $nomEcole }}" class="h-full w-full object-contain">
                                </span>
                            @else
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cortex-rouge/[0.08] text-cortex-rouge">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M5 21V9.5L12 4l7 5.5V21M9.5 21v-5.5h5V21M9.5 12h.01M14.5 12h.01" />
                                    </svg>
                                </span>
                            @endif
                            <span class="flex-1 text-[13.5px] font-semibold leading-snug text-cortex-encre">{{ $nomEcole }}</span>
                            <svg class="h-4 w-4 shrink-0 text-cortex-body/40 transition-transform duration-200 ease-rapide group-hover:translate-x-0.5 group-hover:text-cortex-rouge" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" />
                            </svg>
                        </a>
                    @else
                        <div class="flex items-center gap-3 rounded-xl border border-cortex-border bg-cortex-surface-2 px-4 py-3 opacity-60">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-cortex-body/50">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M5 21V9.5L12 4l7 5.5V21M9.5 21v-5.5h5V21M9.5 12h.01M14.5 12h.01" />
                                </svg>
                            </span>
                            <span class="flex-1 text-[13.5px] font-semibold leading-snug text-cortex-body">{{ $nomEcole }}</span>
                            <span class="shrink-0 text-[11px] font-semibold text-cortex-body/60">Bientôt</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <script>
        (function () {
            var reduit = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (reduit || !('IntersectionObserver' in window)) {
                document.querySelectorAll('.reveler, .trait-timeline').forEach(function (el) {
                    el.classList.add('visible');
                });
                return;
            }

            // Le hero est visible d'emblée : on l'anime au chargement plutôt qu'au scroll.
            requestAnimationFrame(function () {
                document.querySelectorAll('section:first-of-type .reveler').forEach(function (el) {
                    el.classList.add('visible');
                });
            });

            var observateur = new IntersectionObserver(function (entrees) {
                entrees.forEach(function (entree) {
                    if (!entree.isIntersecting) {
                        return;
                    }

                    entree.target.classList.add('visible');
                    observateur.unobserve(entree.target);

                    if (entree.target.hasAttribute('data-compteur')) {
                        compter(entree.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

            document.querySelectorAll('.reveler, .trait-timeline, [data-compteur]').forEach(function (el) {
                observateur.observe(el);
            });

            function compter(el) {
                var cible = parseInt(el.getAttribute('data-compteur'), 10);

                if (!cible) {
                    return;
                }

                var debut = performance.now();
                var duree = 1200;

                function etape(maintenant) {
                    var avancement = Math.min((maintenant - debut) / duree, 1);
                    // Décélération douce, cohérente avec l'easing d'entrée.
                    var adouci = 1 - Math.pow(1 - avancement, 3);
                    el.textContent = Math.round(adouci * cible);

                    if (avancement < 1) {
                        requestAnimationFrame(etape);
                    }
                }

                el.textContent = '0';
                requestAnimationFrame(etape);
            }
        })();
    </script>
</body>
</html>
