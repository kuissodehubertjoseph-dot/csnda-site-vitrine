<?php

return [

    'nom' => 'Cours Secondaire Notre-Dame des Apôtres',
    'sigle' => 'CSNDA',
    'ville' => 'Cotonou · Bénin',
    'slogan' => 'Optimus esse aut non esse',
    'telephone_secretariat' => '+229 00 00 00 00',
    'logo' => 'images/logo-ecole.jpg',

    /*
    |--------------------------------------------------------------------------
    | Classes disponibles
    |--------------------------------------------------------------------------
    | Liste utilisée dans les formulaires (menu déroulant) et les filtres.
    */
    'classes' => [
        'Cours primaire',
        '6ème', '5ème', '4ème', '3ème',
        '2nde A', '2nde B', '2nde C', '2nde D',
        '1ère A', '1ère B', '1ère C', '1ère D',
        'Tle A', 'Tle B', 'Tle C', 'Tle D',
    ],

    /*
    |--------------------------------------------------------------------------
    | Année scolaire courante
    |--------------------------------------------------------------------------
    | Utilisée comme valeur par défaut à la création d'un élève.
    | Format attendu : "AAAA-AAAA".
    */
    'annee_scolaire_courante' => '2026-2027',

];
