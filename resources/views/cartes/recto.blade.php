<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Carte scolaire — Recto — {{ $eleve->nom_complet }}</title>
    @include('cartes.partials.style')
    <style>
        .bandeau-titre {
            background: var(--color-white);
            border-bottom: 0.6mm solid var(--color-green);
            padding: 1.2mm 3mm;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }

        .bandeau-titre .titre {
            font-size: 2.4mm;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: var(--color-ink);
        }

        .bandeau-titre .annee {
            font-size: 2mm;
            font-weight: 600;
            color: var(--color-green-deep);
        }

        .corps {
            flex: 1;
            display: flex;
            align-items: stretch;
            gap: 3mm;
            padding: 2mm 3mm;
        }

        .photo-cadre {
            flex: none;
            width: 19mm;
            height: 23mm;
            border: 0.5mm solid var(--color-green);
            border-radius: 1mm;
            overflow: hidden;
            background: var(--color-white);
        }

        .photo-cadre img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .infos {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1mm;
            min-width: 0;
        }

        .chip-matricule {
            align-self: flex-start;
            background: var(--color-green);
            color: var(--color-white);
            font-size: 2.2mm;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 0.6mm 2mm;
            border-radius: 3mm;
        }

        .nom-eleve {
            font-size: 3.4mm;
            font-weight: 700;
            color: var(--color-sky-deep);
            line-height: 1.15;
            margin-top: 0.5mm;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ligne-info {
            font-size: 2.1mm;
            color: var(--color-ink);
            display: flex;
            gap: 1mm;
        }

        .ligne-info .etiquette {
            font-weight: 600;
            text-transform: uppercase;
            color: var(--color-sky-deep);
            flex: none;
            width: 15mm;
        }

        .ligne-info .valeur {
            font-weight: 700;
            color: var(--color-ink);
        }
    </style>
</head>
<body>
    <div class="carte">
        <div class="bandeau-haut">
            <div class="monogramme">
                <img src="{{ \App\Support\Ecole::logoDataUri() }}" alt="Logo {{ config('ecole.sigle') }}">
            </div>
            <div class="identite-ecole">
                <div class="nom-ecole">{{ config('ecole.sigle') }}</div>
                <div class="sous-texte">{{ config('ecole.ville') }}</div>
            </div>
        </div>

        <div class="bandeau-titre">
            <span class="titre">CARTE D'IDENTITÉ SCOLAIRE</span>
            <span class="annee">{{ $eleve->annee_scolaire }}</span>
        </div>

        <div class="corps">
            <div class="photo-cadre">
                <img src="{{ $eleve->photo_data_uri }}" alt="Photo de {{ $eleve->nom_complet }}">
            </div>

            <div class="infos">
                <span class="chip-matricule">{{ $eleve->matricule }}</span>
                <div class="nom-eleve">{{ mb_strtoupper($eleve->nom) }} {{ $eleve->prenoms }}</div>

                <div class="ligne-info"><span class="etiquette">Né(e) le</span><span class="valeur">{{ $eleve->date_naissance->format('d/m/Y') }}</span></div>
                <div class="ligne-info"><span class="etiquette">À</span><span class="valeur">{{ $eleve->lieu_naissance }}</span></div>
                <div class="ligne-info"><span class="etiquette">Sexe</span><span class="valeur">{{ $eleve->sexe === 'M' ? 'Masculin' : 'Féminin' }}</span></div>
                <div class="ligne-info"><span class="etiquette">Classe</span><span class="valeur">{{ $eleve->classe }}</span></div>
                @if ($eleve->telephone)
                    <div class="ligne-info"><span class="etiquette">Téléphone</span><span class="valeur">{{ $eleve->telephone }}</span></div>
                @endif
            </div>
        </div>

        <div class="bandeau-bas">
            <div class="cachet-zone">Cachet &amp; signature</div>
            <div class="slogan-ecole">
                <div class="nom">{{ config('ecole.sigle') }}</div>
                <div class="slogan">{{ config('ecole.slogan') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
