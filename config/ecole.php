<?php

return [

    'nom' => 'Collège Privé d\'Enseignement Général Catholique Cours de Soutien Scolaire',
    'sigle' => 'CSS',
    'ville' => 'Cotonou · Bénin',
    'slogan' => 'Piété · Travail · Excellence',
    'telephone_secretariat' => '01-97-14-27-27',
    'logo' => 'images/logo-ecole.jpg',

    /*
    |--------------------------------------------------------------------------
    | En-tête officiel (carte d'identité scolaire)
    |--------------------------------------------------------------------------
    | Lignes exactes du cartouche imprimé sur le recto de la carte.
    */
    'tutelle_ligne1' => 'ARCHIDIOCESE DE COTONOU',
    'tutelle_ligne2' => 'DIRECTION DIOCESAINE DE L\'ENSEIGNEMENT CATHOLIQUE',
    'nom_ligne1' => 'COLLEGE PRIVÉ D\'ENSEIGNEMENT GÉNÉRAL',
    'nom_ligne2' => 'CATHOLIQUE COURS DE SOUTIEN SCOLAIRE',

    /*
    |--------------------------------------------------------------------------
    | Certification (verso de la carte)
    |--------------------------------------------------------------------------
    */
    'adresse' => '05 BP 9102 Akpakpa Cotonou',
    'siege_social' => null,
    'telephone_mobile' => null,
    'email' => null,
    'site_web' => null,
    'directeur' => 'Père Jean OUSSOU-KICHO',

    /*
    |--------------------------------------------------------------------------
    | Signature et cachet (verso de la carte)
    |--------------------------------------------------------------------------
    | Importés depuis la page Paramètres. Tant qu'aucun fichier n'a été déposé,
    | l'espace correspondant reste vide sur la carte (voir App\Support\Ecole).
    */
    'signature' => 'images/signature-directeur.png',
    'cachet' => 'images/cachet-ecole.png',

    /*
    |--------------------------------------------------------------------------
    | Classes disponibles
    |--------------------------------------------------------------------------
    | Liste utilisée dans les formulaires (menu déroulant) et les filtres.
    */
    'classes' => [
        'Cours primaire',
        '6ème MA', '6ème MB', '6ème MC', '6ème MD', '6ème ME', '6ème MF',
        '5ème A', '5ème B', '5ème C', '5ème D', '5ème E', '5ème F', '5ème G',
        '4e A', '4e B', '4e C', '4e D', '4e E', '4e F', '4e G', '4e H',
        '3ème A', '3ème B', '3ème C', '3ème D', '3ème E', '3ème F', '3ème G', '3ème H',
        '2nde A2','2nde B1', '2nde C', '2nde D1', '2nde D2', '2nde D3',
        '1ère A2','1ère B1', '1ère B2', '1ère C', '1ère D1', '1ère D2', '1ère D3',
        'Tle A2', 'Tle B1', 'Tle B2', 'Tle C', 'Tle D1', 'Tle D2', 'Tle D3',
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
