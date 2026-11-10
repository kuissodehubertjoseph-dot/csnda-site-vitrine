<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Faculté des Sciences de l'Agronomie et de l'Environnement (FSAE), rattachée
 * à la même tutelle que UCAO/EGEI : reprend exactement les mêmes informations
 * de carte qu'EGEI (tutelle, téléphones, email, adresse, directeur, signature,
 * cachet, logo) — seul le nom de l'école change. La carte (recto/verso) utilise
 * le même gabarit que UCAO/EGEI (slug "fsae" ajouté aux conditions des vues).
 *
 * Les élèves des filières agronomie/environnement, saisis jusque-là dans EGEI,
 * sont déplacés vers la FSAE avec toutes leurs données (photo, signature,
 * matricule...) et ces filières sont retirées de la liste des classes d'EGEI.
 */
return new class extends Migration
{
    private const CLASSES = [
        'Licence 1 (AGRO)',
        'Licence 2 (AGRO)',
        'Licence 3 (PGRA)',
        'Licence 1 (GEAT)',
        'Licence 2 (GEAT)',
        'Licence 3 (GEAT)',
        'Licence 1 (QHSE)',
        'Licence 1 (TOUR)',
    ];

    private const EMAIL_DIRECTION = 'direction.fsae@cortexbenintv.app';

    public function up(): void
    {
        $egei = DB::table('etablissements')->where('slug', 'egei')->first();

        $fsaeId = DB::table('etablissements')->insertGetId([
            'slug' => 'fsae',
            'statut' => 'actif',
            'nom' => "Faculté des Sciences de l'Agronomie et de l'Environnement",
            'sigle' => 'FSAE',
            'ville' => $egei->ville,
            'slogan' => $egei->slogan,
            'telephone_secretariat' => $egei->telephone_secretariat,
            'telephone_mobile' => $egei->telephone_mobile,
            'email' => $egei->email,
            'site_web' => $egei->site_web,
            'logo' => $egei->logo,
            'tutelle_ligne1' => $egei->tutelle_ligne1,
            'tutelle_ligne2' => $egei->tutelle_ligne2,
            'nom_ligne1' => "FACULTE DES SCIENCES DE L'AGRONOMIE",
            'nom_ligne2' => "ET DE L'ENVIRONNEMENT",
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

        DB::table('students')
            ->where('etablissement_id', $egei->id)
            ->whereIn('classe', self::CLASSES)
            ->update(['etablissement_id' => $fsaeId, 'updated_at' => now()]);

        $classesEgei = array_values(array_diff(json_decode($egei->classes, true) ?? [], self::CLASSES));

        DB::table('etablissements')->where('id', $egei->id)->update([
            'classes' => json_encode($classesEgei),
            'updated_at' => now(),
        ]);

        // Mot de passe aléatoire : à définir ensuite par l'administrateur.
        User::updateOrCreate(
            ['email' => self::EMAIL_DIRECTION],
            [
                'name' => 'Direction FSAE',
                'password' => Str::random(40),
                'role' => User::ROLE_DG,
                'etablissement_id' => $fsaeId,
            ]
        );
    }

    public function down(): void
    {
        $egei = DB::table('etablissements')->where('slug', 'egei')->first();
        $fsae = DB::table('etablissements')->where('slug', 'fsae')->first();

        if ($fsae) {
            DB::table('students')
                ->where('etablissement_id', $fsae->id)
                ->update(['etablissement_id' => $egei->id, 'updated_at' => now()]);
        }

        $classesEgei = json_decode($egei->classes, true) ?? [];

        DB::table('etablissements')->where('id', $egei->id)->update([
            'classes' => json_encode(array_values(array_unique(array_merge($classesEgei, self::CLASSES)))),
            'updated_at' => now(),
        ]);

        User::where('email', self::EMAIL_DIRECTION)->delete();
        DB::table('etablissements')->where('slug', 'fsae')->delete();
    }
};
