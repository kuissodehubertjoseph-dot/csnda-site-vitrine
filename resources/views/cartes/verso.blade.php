<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Carte scolaire — Verso — {{ $eleve->nom_complet }}</title>
    @include('cartes.partials.style')
</head>
<body class="ecole-{{ config('ecole.slug') }}">
    <div class="carte carte-verso">
        @include('cartes.partials.verso-contenu')
    </div>
</body>
</html>
