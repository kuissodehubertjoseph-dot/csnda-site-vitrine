<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identité Cortex Bénin TV
    |--------------------------------------------------------------------------
    | Utilisée sur la page d'accueil publique de l'application (avant connexion).
    */
    'nom' => 'CORTEX BÉNIN TV',
    'logo' => 'images/LOGO CORTEX REDUITE copie.png',

    /*
    |--------------------------------------------------------------------------
    | Établissements partenaires
    |--------------------------------------------------------------------------
    | Liste affichée sur la page d'accueil. Noms provisoires à remplacer par
    | les vrais établissements dont Cortex Bénin TV gère les cartes scolaires.
    |
    | Chaque entrée accepte soit un simple nom, soit un tableau
    | ['nom' => ..., 'logo' => 'images/...'] pour afficher le logo de
    | l'établissement à la place de l'icône générique.
    */
    'ecoles' => [
        [
            'nom' => "Collège Privé d'Enseignement Général Catholique Cours de Soutien Scolaire",
            'logo' => 'images/logo-ecole.jpg',
            'slug' => 'css',
            'statut' => 'actif',
        ],
        [
            'nom' => 'Collège Catholique Saint Jean-Baptiste de Cotonou',
            'logo' => 'images/jean.png',
            'slug' => 'jean-baptiste',
            'statut' => 'actif',
        ],
        [
            'nom' => 'UCAO',
            'logo' => 'images/UCAO.png',
            'slug' => 'ucao',
            'statut' => 'actif',
        ],
        [
            'nom' => 'Lycée Les Élites',
            'logo' => 'images/elite.png',
            'slug' => 'lycee-les-elites',
            'statut' => 'actif',
        ],
        [
            'nom' => 'CS Saint Romaric',
            'logo' => 'images/romaric.jpeg',
            'slug' => 'st-romaric',
            'statut' => 'actif',
        ],
        [
            'nom' => 'UCAO-ECOLE DE GENIE ELECTRIQUE ET INFORMATIQUE (EGEI)',
            'logo' => 'images/UCAO.png',
            'slug' => 'egei',
            'statut' => 'actif',
        ],
        [
            'nom' => 'QSI International School of Benin',
            'statut' => 'bientot',
        ],
        [
            'nom' => 'OAK International School',
            'statut' => 'bientot',
        ],
    ],

];
