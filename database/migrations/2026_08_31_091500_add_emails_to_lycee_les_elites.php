<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adresses email officielles du Lycée Les Élites, affichées au verso de la
 * carte scolaire (contact + inscriptions, sur deux lignes).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('etablissements')
            ->where('slug', 'lycee-les-elites')
            ->update([
                'email' => "contact@leselites.bj\ninscriptions@leselites.bj",
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('etablissements')
            ->where('slug', 'lycee-les-elites')
            ->update([
                'email' => null,
                'updated_at' => now(),
            ]);
    }
};
