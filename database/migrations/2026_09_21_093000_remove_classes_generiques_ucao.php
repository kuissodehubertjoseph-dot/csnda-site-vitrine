<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Retire les classes génériques ("Licence 1", "Licence 2", ...) de l'UCAO,
 * désormais remplacées par les filières précises (ex. "Licence 1 - Commerce").
 */
return new class extends Migration
{
    private const CLASSES_GENERIQUES = [
        'Licence 1',
        'Licence 2',
        'Licence 3',
        'Master 1',
        'Master 2',
    ];

    public function up(): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'ucao')->first();

        if (! $etablissement) {
            return;
        }

        $classes = json_decode($etablissement->classes, true) ?? [];
        $classes = array_values(array_diff($classes, self::CLASSES_GENERIQUES));

        DB::table('etablissements')
            ->where('slug', 'ucao')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'ucao')->first();

        if (! $etablissement) {
            return;
        }

        $classes = json_decode($etablissement->classes, true) ?? [];

        foreach (self::CLASSES_GENERIQUES as $classe) {
            if (! in_array($classe, $classes, true)) {
                $classes[] = $classe;
            }
        }

        DB::table('etablissements')
            ->where('slug', 'ucao')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);
    }
};
