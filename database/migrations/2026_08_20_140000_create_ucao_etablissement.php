<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Deuxième établissement réel de l'application (après CSS) : UCAO — École
 * Supérieure de Management et d'Économie Appliquée (UCAO-ESMEA). Les textes
 * de nom_ligne1/2, tutelle_ligne1/2 et les coordonnées (siège social,
 * téléphones, email, site) reproduisent exactement la carte d'apprenant et le
 * cartouche officiel fournis par l'établissement. Le slogan et la liste des
 * filières restent provisoires — à corriger depuis la base ou une future page
 * d'administration.
 */
return new class extends Migration
{
    public function up(): void
    {
        $etablissementId = DB::table('etablissements')->insertGetId([
            'slug' => 'ucao',
            'statut' => 'actif',
            'nom' => "École Supérieure de Management et d'Économie Appliquée (UCAO-ESMEA)",
            'sigle' => 'UCAO',
            'ville' => 'Cotonou · Bénin',
            'slogan' => 'Savoir · Foi · Excellence',
            'telephone_secretariat' => '(00229) 01 21 60 40 70',
            'telephone_mobile' => '01 56 35 14 41',
            'email' => 'contact@ucaobenin.org',
            'site_web' => 'www.ucaobenin.org',
            'logo' => 'images/UCAO.png',
            'tutelle_ligne1' => "UNIVERSITE CATHOLIQUE DE L'AFRIQUE DE L'OUEST",
            'tutelle_ligne2' => 'UNITE UNIVERSITAIRE A COTONOU',
            'nom_ligne1' => 'ECOLE SUPERIEURE DE MANAGEMENT',
            'nom_ligne2' => "ET D'ECONOMIE APPLIQUEE",
            'siege_social' => 'Lot 246, Saint Jean Gbèdiga',
            'adresse' => "Rue de l'hôpital Saint Jean\n04 BP 928 Cotonou-Rép. Bénin",
            'directeur' => 'Pr.Dr.ir. ASSOGBA Kokou',
            'signature' => 'images/signature-ucao.png',
            'cachet' => 'images/cachet-ucao.png',
            'classes' => json_encode([
                'Licence 1', 'Licence 2', 'Licence 3',
                'Master 1', 'Master 2',
            ]),
            'annee_scolaire_courante' => '2026-2027',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::updateOrCreate(
            ['email' => 'direction.ucao@cortexbenintv.app'],
            [
                'name' => 'Direction UCAO',
                'password' => 'Ucao2026@',
                'role' => User::ROLE_DG,
                'etablissement_id' => $etablissementId,
            ]
        );
    }

    public function down(): void
    {
        User::where('email', 'direction.ucao@cortexbenintv.app')->delete();
        DB::table('etablissements')->where('slug', 'ucao')->delete();
    }
};
