<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nouvel établissement : CS Saint Romaric. Aucune donnée officielle fournie
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
            'slug' => 'st-romaric',
            'statut' => 'actif',
            'nom' => 'CS Saint Romaric',
            'sigle' => 'CSR',
            'ville' => 'Cotonou · Bénin',
            'slogan' => 'Travail · Excellence · Réussite',
            'telephone_secretariat' => null,
            'email' => null,
            'logo' => null,
            'tutelle_ligne1' => 'REPUBLIQUE DU BENIN',
            'tutelle_ligne2' => 'MINISTERE DES ENSEIGNEMENTS SECONDAIRE, TECHNIQUE ET DE LA FORMATION PROFESSIONNELLE',
            'nom_ligne1' => 'CS',
            'nom_ligne2' => 'SAINT ROMARIC',
            'adresse' => null,
            'directeur' => null,
            'signature' => null,
            'cachet' => null,
            'classes' => json_encode([
                'CI', 'CP', 'CE1', 'CE2', 'CM1', 'CM2',
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
            ['email' => 'direction.stromaric@cortexbenintv.app'],
            [
                'name' => 'Direction CS Saint Romaric',
                'password' => 'Romaric2026@',
                'role' => User::ROLE_DG,
                'etablissement_id' => $etablissementId,
            ]
        );
    }

    public function down(): void
    {
        User::where('email', 'direction.stromaric@cortexbenintv.app')->delete();
        DB::table('etablissements')->where('slug', 'st-romaric')->delete();
    }
};
