<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Faculté de Droit et d'Économie (FDE), rattachée à la même tutelle que
 * UCAO/EGEI/FSAE : reprend exactement les mêmes informations de carte qu'EGEI
 * (tutelle, téléphones, email, adresse, directeur, signature, cachet, logo) —
 * seul le nom de l'école change. Carte au gabarit UCAO (slug "fde" ajouté aux
 * conditions des vues).
 *
 * Remplace CS Saint Romaric sur la page d'accueil : Saint Romaric (aucun
 * élève) est désactivé mais conservé en base avec son compte, pour pouvoir
 * être réactivé.
 */
return new class extends Migration
{
    private const CLASSES = [
        'Licence 1 (DRT)',
        'Licence 2 (DRT)',
        'Licence 3 (DRT)',
        'Licence 1 (ECO)',
        'Licence 2 (ECO)',
        'Licence 1 (ESA)',
        'Master 1 (DPRF)',
        'Master 1 (DPUF)',
    ];

    private const EMAIL_DIRECTION = 'direction.fde@cortexbenintv.app';

    public function up(): void
    {
        $egei = DB::table('etablissements')->where('slug', 'egei')->first();

        $fdeId = DB::table('etablissements')->insertGetId([
            'slug' => 'fde',
            'statut' => 'actif',
            'nom' => "FACULTE DE DROIT ET D'ECONOMIE",
            'sigle' => 'FDE',
            'ville' => $egei->ville,
            'slogan' => $egei->slogan,
            'telephone_secretariat' => $egei->telephone_secretariat,
            'telephone_mobile' => $egei->telephone_mobile,
            'email' => $egei->email,
            'site_web' => $egei->site_web,
            'logo' => $egei->logo,
            'tutelle_ligne1' => $egei->tutelle_ligne1,
            'tutelle_ligne2' => $egei->tutelle_ligne2,
            'nom_ligne1' => 'FACULTE DE DROIT',
            'nom_ligne2' => "ET D'ECONOMIE",
            'siege_social' => $egei->siege_social,
            'adresse' => $egei->adresse,
            'directeur' => $egei->directeur,
            'signature' => $egei->signature,
            'cachet' => $egei->cachet,
            'classes' => json_encode(self::CLASSES),
            'annee_scolaire_courante' => $egei->annee_scolaire_courante,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mot de passe aléatoire : à définir ensuite par l'administrateur.
        User::updateOrCreate(
            ['email' => self::EMAIL_DIRECTION],
            [
                'name' => 'Direction FDE',
                'password' => Str::random(40),
                'role' => User::ROLE_DG,
                'etablissement_id' => $fdeId,
            ]
        );

        DB::table('etablissements')->where('slug', 'st-romaric')->update([
            'statut' => 'bientot',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('etablissements')->where('slug', 'st-romaric')->update([
            'statut' => 'actif',
            'updated_at' => now(),
        ]);

        User::where('email', self::EMAIL_DIRECTION)->delete();
        DB::table('etablissements')->where('slug', 'fde')->delete();
    }
};
