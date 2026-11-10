<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Deuxième exemplaire des écoles UCAO, FDE, EGEI et FSAE : chaque copie
 * reprend exactement les informations de l'originale (logo, tutelle, nom en
 * bas de carte, adresse, directeur, signature, cachet, classes...) — seul le
 * nom affiché reçoit le suffixe " 2" et le slug "-2". Les élèves ne sont pas
 * copiés : chaque copie démarre vide. La carte (recto/verso) utilise le même
 * gabarit que l'originale (slugs "-2" ajoutés aux conditions des vues).
 */
return new class extends Migration
{
    /** slug de l'école d'origine => email du compte direction de la copie */
    private const ECOLES = [
        'ucao' => 'direction.ucao2@cortexbenintv.app',
        'fde' => 'direction.fde2@cortexbenintv.app',
        'egei' => 'direction.egei2@cortexbenintv.app',
        'fsae' => 'direction.fsae2@cortexbenintv.app',
    ];

    public function up(): void
    {
        foreach (self::ECOLES as $slug => $emailDirection) {
            $origine = (array) DB::table('etablissements')->where('slug', $slug)->first();

            unset($origine['id']);

            $copieId = DB::table('etablissements')->insertGetId(array_merge($origine, [
                'slug' => $slug.'-2',
                'nom' => $origine['nom'].' 2',
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Mot de passe aléatoire : à définir ensuite par l'administrateur.
            User::updateOrCreate(
                ['email' => $emailDirection],
                [
                    'name' => 'Direction '.$origine['sigle'].' 2',
                    'password' => Str::random(40),
                    'role' => User::ROLE_DG,
                    'etablissement_id' => $copieId,
                ]
            );
        }
    }

    public function down(): void
    {
        foreach (self::ECOLES as $slug => $emailDirection) {
            User::where('email', $emailDirection)->delete();
            DB::table('etablissements')->where('slug', $slug.'-2')->delete();
        }
    }
};
