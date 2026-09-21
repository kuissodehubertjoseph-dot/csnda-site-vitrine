<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute la classe "Licence 1 - Action Commerciale et Force de Vente" à la
 * liste des classes de l'UCAO-ESMEA.
 */
return new class extends Migration
{
    public function up(): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'ucao')->first();

        if (! $etablissement) {
            return;
        }

        $classes = json_decode($etablissement->classes, true) ?? [];

        if (! in_array('Licence 1 - Action Commerciale et Force de Vente', $classes, true)) {
            $classes[] = 'Licence 1 - Action Commerciale et Force de Vente';
        }

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
        $classes = array_values(array_diff($classes, ['Licence 1 - Action Commerciale et Force de Vente']));

        DB::table('etablissements')
            ->where('slug', 'ucao')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);
    }
};
