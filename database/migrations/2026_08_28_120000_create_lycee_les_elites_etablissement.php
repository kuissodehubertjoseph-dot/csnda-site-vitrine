<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nouvel établissement : Lycée Les Élites. Aucune donnée officielle fournie
 * par l'établissement à ce stade (tutelle, adresse, directeur, classes) : les
 * valeurs ci-dessous sont provisoires, à corriger depuis la base ou une
 * future page d'administration. Utilise le gabarit de carte générique
 * (CARTE D'IDENTITÉ SCOLAIRE, voir cartes.partials.recto-contenu /
 * verso-contenu — branche "else", partagée par tout établissement dont le
 * slug n'est ni "ucao" ni "jean-baptiste").
 */
return new class extends Migration
{
    public function up(): void
    {
        $etablissementId = DB::table('etablissements')->insertGetId([
            'slug' => 'lycee-les-elites',
            'statut' => 'actif',
            'nom' => 'Lycée Les Élites',
            'sigle' => 'LLE',
            'ville' => 'Cotonou · Bénin',
            'slogan' => 'Excellence · Discipline · Réussite',
            'telephone_secretariat' => null,
            'email' => null,
            'logo' => null,
            'tutelle_ligne1' => 'REPUBLIQUE DU BENIN',
            'tutelle_ligne2' => 'MINISTERE DES ENSEIGNEMENTS SECONDAIRE, TECHNIQUE ET DE LA FORMATION PROFESSIONNELLE',
            'nom_ligne1' => 'LYCEE',
            'nom_ligne2' => 'LES ELITES',
            'adresse' => null,
            'directeur' => null,
            'signature' => null,
            'cachet' => null,
            'classes' => json_encode([
                '6ème', '5ème', '4ème', '3ème',
                '2nde A', '2nde C',
                '1ère A', '1ère D',
                'Tle A', 'Tle D',
            ]),
            'annee_scolaire_courante' => '2026-2027',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::updateOrCreate(
            ['email' => 'direction.lesElites@cortexbenintv.app'],
            [
                'name' => 'Direction Lycée Les Élites',
                'password' => 'Elites2026@',
                'role' => User::ROLE_DG,
                'etablissement_id' => $etablissementId,
            ]
        );
    }

    public function down(): void
    {
        User::where('email', 'direction.lesElites@cortexbenintv.app')->delete();
        DB::table('etablissements')->where('slug', 'lycee-les-elites')->delete();
    }
};
