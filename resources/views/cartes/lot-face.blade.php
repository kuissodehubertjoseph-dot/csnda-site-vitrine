<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Cartes scolaires — Lot ({{ $face === 'verso' ? 'Verso' : 'Recto' }})</title>
    @include('cartes.partials.style')
    <style>
        @page { size: 85.6mm 55mm; margin: 0; }

        body { width: auto; height: auto; }

        .page {
            width: 85.6mm;
            height: 55mm;
            page-break-after: always;
        }
        .page:last-child { page-break-after: auto; }
    </style>
</head>
<body class="ecole-{{ config('ecole.slug') }}">
    {{-- Fichier dédié à une seule face (recto ou verso) : une carte par page,
    format CR80. Voir CardController::genererLot(), qui génère un PDF recto et
    un PDF verso séparés par paquet, plutôt qu'un fichier mêlant les deux. --}}
    @if ($face === 'verso')
        @foreach ($eleves as $eleve)
            <div class="page carte carte-verso">
                @include('cartes.partials.verso-contenu')
            </div>
        @endforeach
    @else
        @php($logoDataUri = \App\Support\Ecole::logoDataUri())
        @foreach ($eleves as $eleve)
            <div class="page carte carte-recto"
                @if (config('ecole.slug') === 'jean-baptiste')
                    style="--jb-logo-fond: url('{{ $logoDataUri }}')"
                @endif
            >
                @include('cartes.partials.recto-contenu', ['eleve' => $eleve, 'logoDataUri' => $logoDataUri])
            </div>
        @endforeach
    @endif
</body>
</html>
