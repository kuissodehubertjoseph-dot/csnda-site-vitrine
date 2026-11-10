<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Troisième exemplaire des écoles UCAO, FDE, EGEI et FSAE : reprend les
 * paramètres des copies version 2 sans copier leurs élèves.
 */
return new class extends Migration
{
    /** slug de l'école d'origine => email du compte direction de la copie */
    private const ECOLES = [
        'ucao' => 'direction.ucao3@cortexbenintv.app',
        'fde' => 'direction.fde3@cortexbenintv.app',
        'egei' => 'direction.egei3@cortexbenintv.app',
        'fsae' => 'direction.fsae3@cortexbenintv.app',
    ];

    public function up(): void
    {
        foreach (self::ECOLES as $slug => $emailDirection) {
            $modele = DB::table('etablissements')->where('slug', $slug.'-2')->first();

            if (! $modele) {
                throw new RuntimeException("L'établissement {$slug}-2 est introuvable.");
            }

            $copie = (array) $modele;

            unset($copie['id']);

            if (! str_ends_with($copie['nom'], ' 2')) {
                throw new RuntimeException("Le nom de l'établissement {$slug}-2 ne se termine pas par « 2 ».");
            }

            $copieId = DB::table('etablissements')->insertGetId(array_merge($copie, [
                'slug' => $slug.'-3',
                'nom' => substr($copie['nom'], 0, -1).'3',
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            User::updateOrCreate(
                ['email' => $emailDirection],
                [
                    'name' => 'Direction '.$copie['sigle'].' 3',
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
            DB::table('etablissements')->where('slug', $slug.'-3')->delete();
        }
    }
};
