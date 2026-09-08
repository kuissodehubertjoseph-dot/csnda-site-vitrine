<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Retrait des adresses email du verso de la carte du Lycée Les Élites,
 * sur demande de l'établissement.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('etablissements')
            ->where('slug', 'lycee-les-elites')
            ->update([
                'email' => null,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('etablissements')
            ->where('slug', 'lycee-les-elites')
            ->update([
                'email' => "contact@leselites.bj\ninscriptions@leselites.bj",
                'updated_at' => now(),
            ]);
    }
};
