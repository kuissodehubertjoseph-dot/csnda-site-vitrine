@if (config('ecole.slug') === 'ucao')
    {{-- Verso UCAO : reproduction exacte du cartouche officiel fourni par
    l'établissement — fond blanc uni (pas de dégradé, la carte physique n'est
    pas en PVC couleur des deux côtés), texte centré uniquement. --}}
    <div class="ucao-verso-papier">
        <div class="ucao-verso-entete">{{ config('ecole.tutelle_ligne1') }}</div>
        <div class="ucao-verso-entete">{{ config('ecole.tutelle_ligne2') }}</div>

        <div class="ucao-verso-coordonnees">
            @if (config('ecole.siege_social'))
                <div>Siège social : {{ config('ecole.siege_social') }}</div>
            @endif
            @foreach (explode("\n", (string) config('ecole.adresse')) as $ligneAdresse)
                @if (trim($ligneAdresse) !== '')
                    <div>{{ trim($ligneAdresse) }}</div>
                @endif
            @endforeach
            @if (config('ecole.telephone_secretariat') || config('ecole.telephone_mobile'))
                <div>
                    @if (config('ecole.telephone_secretariat'))
                        Tél. : {{ config('ecole.telephone_secretariat') }}
                    @endif
                    @if (config('ecole.telephone_mobile'))
                        Mobile : {{ config('ecole.telephone_mobile') }}
                    @endif
                </div>
            @endif
            @if (config('ecole.email'))
                <div>Email : {{ config('ecole.email') }}</div>
            @endif
            @if (config('ecole.site_web'))
                <div>Site : {{ config('ecole.site_web') }}</div>
            @endif
        </div>
    </div>
@elseif (config('ecole.slug') === 'jean-baptiste')
    {{-- Verso Saint Jean-Baptiste : reproduction du cartouche officiel fourni
    par l'établissement (photo de la carte physique) — titre bleu en deux
    lignes, texte de certification citant le directeur nommément, signature +
    cachet qui se chevauchent en bas à droite, coordonnées en rouge en pied
    de carte. --}}
    <div class="jb-verso-papier">
        <div class="jb-verso-titre jb-verso-titre-1">COLLÈGE CATHOLIQUE</div>
        <div class="jb-verso-titre">SAINT JEAN-BAPTISTE</div>

        <div class="jb-verso-texte">
            <span class="jb-verso-texte-ligne1">Je soussigné, {{ config('ecole.directeur') }}, Directeur du</span>
            <span class="jb-verso-texte-suite">Collège Catholique Saint Jean-Baptiste, certifie que le (la)
            titulaire de cette carte est élève dans mon établissement.</span>
        </div>

        <div class="jb-verso-signature-zone">
            <div class="jb-verso-le-directeur">Le Directeur</div>
            <div class="jb-verso-cachet">
                @if ($cachet = \App\Support\Ecole::cachetDataUri())
                    <img src="{{ $cachet }}" alt="Cachet de l'établissement" class="jb-verso-cachet-img">
                @endif
                @if ($signature = \App\Support\Ecole::signatureDataUri())
                    <img src="{{ $signature }}" alt="Signature du directeur" class="jb-verso-signature-img">
                @endif
            </div>
            <div class="jb-verso-directeur">{{ config('ecole.directeur') }}</div>
        </div>

        <div class="jb-verso-pied">
            {{ config('ecole.adresse') }}
            @if (config('ecole.telephone_secretariat'))
                - Tél. : {{ config('ecole.telephone_secretariat') }}
            @endif
            @if (config('ecole.email'))
                - E-mail : {{ config('ecole.email') }}
            @endif
        </div>
    </div>
@else
    {{-- Verso : contenu fixe (certification), identique pour toutes les cartes CSS. --}}
    <div class="cadre-verso">
        <div class="verso-republique">
            @if (config('ecole.slug') === 'lycee-les-elites')
                LYCEE LES ELITES
            @else
                RÉPUBLIQUE DU BÉNIN
            @endif
        </div>
        <div class="verso-trait"></div>

        <div class="verso-texte">
            Je soussigné (e), Le Directeur du {{ config('ecole.nom_ligne1') }}<br>
            {{ config('ecole.nom_ligne2') }} certifie que le (la)<br>
            titulaire de cette carte est élève dans mon établissement.
        </div>

        <div class="verso-adresse">
            {{ config('ecole.adresse') }}<br>
            Tél : {{ config('ecole.telephone_secretariat') }}
            @if (config('ecole.telephone_mobile'))
                <br>{{ config('ecole.telephone_mobile') }}
            @endif
            @if (config('ecole.email'))
                @foreach (explode("\n", (string) config('ecole.email')) as $ligneEmail)
                    @if (trim($ligneEmail) !== '')
                        <br>{{ trim($ligneEmail) }}
                    @endif
                @endforeach
            @endif
        </div>

        <div class="verso-cachet">
            @if ($cachet = \App\Support\Ecole::cachetDataUri())
                <img src="{{ $cachet }}" alt="Cachet de l'établissement" class="verso-cachet-img">
            @endif
            @if ($signature = \App\Support\Ecole::signatureDataUri())
                <img src="{{ $signature }}" alt="Signature du directeur" class="verso-signature-img">
            @endif
        </div>

        <div class="verso-directeur">{{ config('ecole.directeur') }}</div>
    </div>
@endif
