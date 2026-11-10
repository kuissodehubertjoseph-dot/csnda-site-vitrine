<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nom affiché de la Faculté des Sciences de l'Agronomie et de l'Environnement
 * raccourci en "FSAE" (barre de navigation, page de connexion). Le nom complet
 * en bas de carte (nom_ligne1/2) n'est pas modifié.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('etablissements')->where('slug', 'fsae')->update([
            'nom' => 'FSAE',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('etablissements')->where('slug', 'fsae')->update([
            'nom' => "Faculté des Sciences de l'Agronomie et de l'Environnement",
            'updated_at' => now(),
        ]);
    }
};
