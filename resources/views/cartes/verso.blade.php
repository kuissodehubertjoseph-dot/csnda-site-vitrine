<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Carte scolaire — Verso — {{ $eleve->nom_complet }}</title>
    @include('cartes.partials.style')
    <style>
        .bandeau-titre-verso {
            background: linear-gradient(135deg, var(--color-green) 0%, var(--color-green-deep) 100%);
            color: var(--color-white);
            padding: 1.6mm 3mm;
            font-size: 2.6mm;
            font-weight: 700;
            letter-spacing: 0.8px;
        }

        .corps-verso {
            flex: 1;
            display: flex;
            gap: 2.5mm;
            padding: 2mm 3mm;
        }

        .colonne-reglement {
            flex: 1.3;
            min-width: 0;
        }

        .colonne-reglement h3,
        .contact h3 {
            font-size: 2mm;
            font-weight: 700;
            color: var(--color-sky-deep);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 1mm 0;
        }

        .colonne-reglement ul {
            margin: 0;
            padding-left: 3mm;
            font-size: 1.8mm;
            line-height: 1.5;
            color: var(--color-ink);
        }

        .colonne-reglement li {
            margin-bottom: 0.6mm;
        }

        .qr-placeholder {
            flex: none;
            width: 16mm;
            height: 16mm;
            border: 0.3mm dashed var(--color-sky);
            border-radius: 1mm;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5mm;
            color: var(--color-sky-deep);
            text-align: center;
            padding: 1mm;
        }

        .bloc-droit {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .contact {
            font-size: 1.9mm;
            line-height: 1.5;
            color: var(--color-ink);
        }

        .contact .nom {
            font-weight: 700;
            color: var(--color-sky-deep);
        }

        .pied-de-page {
            margin-top: auto;
            background: var(--color-salmon);
            padding: 1mm 3mm;
            font-size: 1.6mm;
            text-align: center;
            color: var(--color-white);
        }
    </style>
</head>
<body>
    <div class="carte">
        <div class="bandeau-titre-verso">RÈGLEMENT &amp; VÉRIFICATION</div>

        <div class="corps-verso">
            <div class="colonne-reglement">
                <h3>Règlement</h3>
                <ul>
                    <li>Carte personnelle et non cessible.</li>
                    <li>À présenter systématiquement à l'entrée de l'établissement.</li>
                    <li>En cas de perte, en informer immédiatement le secrétariat.</li>
                </ul>
            </div>

            <div class="bloc-droit">
                <!-- QR_CODE_PLACEHOLDER -->
                <div class="qr-placeholder">QR code<br>(à venir)</div>

                <div class="contact">
                    <div class="nom">{{ config('ecole.sigle') }}</div>
                    <div>{{ config('ecole.ville') }}</div>
                    <div>Tél. secrétariat : {{ config('ecole.telephone_secretariat') }}</div>
                </div>
            </div>
        </div>

        <div class="pied-de-page">
            Carte propriété de l'établissement — à restituer en fin d'année
        </div>
    </div>
</body>
</html>
