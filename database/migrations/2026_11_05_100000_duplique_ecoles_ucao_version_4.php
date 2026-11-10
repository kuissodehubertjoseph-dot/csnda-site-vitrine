<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Quatrième exemplaire des écoles UCAO, FDE, EGEI et FSAE : reprend les
 * paramètres (cartes, filières) des copies version 3 sans copier leurs élèves.
 */
return new class extends Migration
{
    /** slug de l'école d'origine => email du compte direction de la copie */
    private const ECOLES = [
        'ucao' => 'direction.ucao4@cortexbenintv.app',
        'fde' => 'direction.fde4@cortexbenintv.app',
        'egei' => 'direction.egei4@cortexbenintv.app',
        'fsae' => 'direction.fsae4@cortexbenintv.app',
    ];

    public function up(): void
    {
        foreach (self::ECOLES as $slug => $emailDirection) {
            $modele = DB::table('etablissements')->where('slug', $slug.'-3')->first();

            if (! $modele) {
                throw new RuntimeException("L'établissement {$slug}-3 est introuvable.");
            }

            $copie = (array) $modele;

            unset($copie['id']);

            if (! str_ends_with($copie['nom'], ' 3')) {
                throw new RuntimeException("Le nom de l'établissement {$slug}-3 ne se termine pas par « 3 ».");
            }

            $copieId = DB::table('etablissements')->insertGetId(array_merge($copie, [
                'slug' => $slug.'-4',
                'nom' => substr($copie['nom'], 0, -1).'4',
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            User::updateOrCreate(
                ['email' => $emailDirection],
                [
                    'name' => 'Direction '.$copie['sigle'].' 4',
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
            DB::table('etablissements')->where('slug', $slug.'-4')->delete();
        }
    }
};
