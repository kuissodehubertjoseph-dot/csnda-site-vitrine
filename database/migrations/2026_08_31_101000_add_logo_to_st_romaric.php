<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Logo officiel de CS Saint Romaric, fourni par l'établissement.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('etablissements')
            ->where('slug', 'st-romaric')
            ->update([
                'logo' => 'images/romaric.jpeg',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('etablissements')
            ->where('slug', 'st-romaric')
            ->update([
                'logo' => null,
                'updated_at' => now(),
            ]);
    }
};
