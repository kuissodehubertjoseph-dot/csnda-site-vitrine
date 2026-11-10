<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Déplace les élèves des filières droit/économie, saisis jusque-là dans EGEI,
 * vers la Faculté de Droit et d'Économie (FDE) avec toutes leurs données
 * (photo, signature, matricule...), et retire ces filières de la liste des
 * classes d'EGEI — comme pour la FSAE.
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

    public function up(): void
    {
        $egei = DB::table('etablissements')->where('slug', 'egei')->first();
        $fde = DB::table('etablissements')->where('slug', 'fde')->first();

        DB::table('students')
            ->where('etablissement_id', $egei->id)
            ->whereIn('classe', self::CLASSES)
            ->update(['etablissement_id' => $fde->id, 'updated_at' => now()]);

        DB::table('etablissements')->where('id', $egei->id)->update([
            'classes' => json_encode(array_values(array_diff(json_decode($egei->classes, true) ?? [], self::CLASSES))),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $egei = DB::table('etablissements')->where('slug', 'egei')->first();
        $fde = DB::table('etablissements')->where('slug', 'fde')->first();

        DB::table('students')
            ->where('etablissement_id', $fde->id)
            ->whereIn('classe', self::CLASSES)
            ->update(['etablissement_id' => $egei->id, 'updated_at' => now()]);

        DB::table('etablissements')->where('id', $egei->id)->update([
            'classes' => json_encode(array_values(array_unique(array_merge(json_decode($egei->classes, true) ?? [], self::CLASSES)))),
            'updated_at' => now(),
        ]);
    }
};
