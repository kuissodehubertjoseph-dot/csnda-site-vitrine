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
