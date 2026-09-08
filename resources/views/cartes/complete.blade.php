{{-- Carte complète (recto + verso) d'un seul élève, pour export PDF 2 pages --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Carte scolaire — {{ $eleve->nom_complet }}</title>
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
    {{-- Page 1 : recto --}}
    <div class="page carte carte-recto"
        @if (config('ecole.slug') === 'jean-baptiste')
            style="--jb-logo-fond: url('{{ \App\Support\Ecole::logoDataUri() }}')"
        @endif
    >
        @include('cartes.partials.recto-contenu', ['eleve' => $eleve])
    </div>

    {{-- Page 2 : verso --}}
    <div class="page carte carte-verso">
        @include('cartes.partials.verso-contenu')
    </div>
</body>
</html>    
