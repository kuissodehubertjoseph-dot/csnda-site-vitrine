<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Troisième établissement réel de l'application (après CSS et UCAO) :
 * Collège Catholique Saint Jean-Baptiste de Cotonou. Comme pour CSS, on
 * réutilise le gabarit de carte générique (CARTE D'IDENTITÉ SCOLAIRE, voir
 * cartes.partials.recto-contenu / verso-contenu — branche "else", partagée
 * par tout établissement dont le slug n'est pas "ucao").
 *
 * Coordonnées, adresse, directeur et liste de classes provisoires — aucune
 * donnée officielle fournie par l'établissement à ce stade, à corriger
 * depuis la base ou une future page d'administration.
 */
return new class extends Migration
{
    public function up(): void
    {
        $etablissementId = DB::table('etablissements')->insertGetId([
            'slug' => 'jean-baptiste',
            'statut' => 'actif',
            'nom' => 'Collège Catholique Saint Jean-Baptiste de Cotonou',
            'sigle' => 'CCJB',
            'ville' => 'Cotonou · Bénin',
            'slogan' => 'Foi · Discipline · Excellence',
            'telephone_secretariat' => '01 92 76 45 45',
            'email' => 'collegesaintjeanb@gmail.com',
            'logo' => 'images/jean.png',
            'tutelle_ligne1' => 'REPUBLIQUE DU BENIN',
            'tutelle_ligne2' => "DIRECTION DIOCESAINE DE L'ENSEIGNEMENT CATHOLIQUE",
            'nom_ligne1' => 'ARCHIDIOCESE DE COTONOU',
            'nom_ligne2' => 'COLLEGE CATHOLIQUE SAINT JEAN-BAPTISTE',
            'adresse' => '01 B.P 65 Cotonou',
            'directeur' => 'Abbé Maurel Rolland ADJALIAN',
            'signature' => 'images/signature-jean-baptiste.png',
            'cachet' => 'images/cachet-jean-baptiste.png',
            'classes' => json_encode([
                '1ère-A2', '1ère-B', '1ère-C', '1ère-D1', '1ère-D2', '1ère-F4', '1ère-G', '1ère-MMV',
                '2nde-AB', '2nde-C', '2nde-D1', '2nde-D2', '2nde-F3', '2nde-F4', '2nde-G2', '2nde-HR', '2nde-MMV',
                '3ème-E1', '3ème-E2', '3ème-E3', '3ème-E4', '3ème-E5', '3ème-E6',
                '4ème-E1', '4ème-E2', '4ème-E3', '4ème-E4', '4ème-E5',
                '5ème-E1', '5ème-E2', '5ème-E3', '5ème-E4', '5ème-E5',
                '6ème-E1', '6ème-E2', '6ème-E3', '6ème-E4', '6ème-E5',
                'Tle-A2', 'Tle-B', 'Tle-C', 'Tle-D1', 'Tle-D2', 'Tle-F4', 'Tle-G', 'Tle-MMV',
            ]),
            'annee_scolaire_courante' => '2026-2027',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::updateOrCreate(
            ['email' => 'direction.jeanbaptiste@cortexbenintv.app'],
            [
                'name' => 'Direction Saint Jean-Baptiste',
                'password' => 'JeanB2026@',
                'role' => User::ROLE_DG,
                'etablissement_id' => $etablissementId,
            ]
        );
    }

    public function down(): void
    {
        User::where('email', 'direction.jeanbaptiste@cortexbenintv.app')->delete();
        DB::table('etablissements')->where('slug', 'jean-baptiste')->delete();
    }
};
