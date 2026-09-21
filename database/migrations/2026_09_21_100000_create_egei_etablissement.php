<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * UCAO-École de Génie Électrique et Informatique (EGEI), rattachée à la même
 * tutelle que UCAO-ESMEA : reprend exactement les mêmes tutelle_ligne1/2,
 * téléphones, email, adresse et directeur que UCAO (à la demande explicite),
 * et utilise temporairement le même logo (images/UCAO.png) en attendant que
 * l'établissement fournisse le sien. La carte (recto/verso) partage le même
 * gabarit que UCAO — voir cartes.partials.recto-contenu / verso-contenu,
 * dont la condition `config('ecole.slug') === 'ucao'` a été étendue au slug
 * "egei". Filières provisoires (génériques), à corriger depuis la base ou
 * une future page d'administration.
 */
return new class extends Migration
{
    public function up(): void
    {
        $etablissementId = DB::table('etablissements')->insertGetId([
            'slug' => 'egei',
            'statut' => 'actif',
            'nom' => 'UCAO-École de Génie Électrique et Informatique (EGEI)',
            'sigle' => 'EGEI',
            'ville' => 'Cotonou · Bénin',
            'slogan' => 'Savoir · Foi · Excellence',
            'telephone_secretariat' => '(00229) 01 21 60 40 70',
            'telephone_mobile' => '01 56 35 14 41',
            'email' => 'contact@ucaobenin.org',
            'site_web' => 'www.ucaobenin.org',
            'logo' => 'images/UCAO.png',
            'tutelle_ligne1' => "UNIVERSITE CATHOLIQUE DE L'AFRIQUE DE L'OUEST",
            'tutelle_ligne2' => 'UNITE UNIVERSITAIRE A COTONOU',
            'nom_ligne1' => 'ECOLE DE GENIE ELECTRIQUE',
            'nom_ligne2' => 'ET INFORMATIQUE',
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
            ['email' => 'direction.egei@cortexbenintv.app'],
            [
                'name' => 'Direction EGEI',
                'password' => 'Egei2026@',
                'role' => User::ROLE_DG,
                'etablissement_id' => $etablissementId,
            ]
        );
    }

    public function down(): void
    {
        User::where('email', 'direction.egei@cortexbenintv.app')->delete();
        DB::table('etablissements')->where('slug', 'egei')->delete();
    }
};
