<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Numéros de téléphone officiels du Lycée Les Élites, affichés au verso de
 * la carte scolaire (secrétariat + mobile, sur deux lignes).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('etablissements')
            ->where('slug', 'lycee-les-elites')
            ->update([
                'telephone_secretariat' => '+229 97 00 00 00',
                'telephone_mobile' => '+229 61 00 00 00',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('etablissements')
            ->where('slug', 'lycee-les-elites')
            ->update([
                'telephone_secretariat' => null,
                'telephone_mobile' => null,
                'updated_at' => now(),
            ]);
    }
};
