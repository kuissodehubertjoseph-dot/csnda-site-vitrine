<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Carte scolaire — Recto — {{ $eleve->nom_complet }}</title>
    @include('cartes.partials.style')
</head>
<body class="ecole-{{ config('ecole.slug') }}">
    <div class="carte carte-recto"
        @if (config('ecole.slug') === 'jean-baptiste')
            style="--jb-logo-fond: url('{{ \App\Support\Ecole::logoDataUri() }}')"
        @elseif (config('ecole.slug') === 'lycee-les-elites')
            style="--lle-logo-fond: url('{{ \App\Support\Ecole::logoDataUri() }}')"
        @endif
    >
        @include('cartes.partials.recto-contenu', ['eleve' => $eleve])
    </div>
</body>
</html>
