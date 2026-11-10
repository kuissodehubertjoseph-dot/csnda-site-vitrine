<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nom affiché de la Faculté de Droit et d'Économie raccourci en "FDE" (barre
 * de navigation, page de connexion). Le nom complet en bas de carte
 * (nom_ligne1/2) n'est pas modifié.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('etablissements')->where('slug', 'fde')->update([
            'nom' => 'FDE',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('etablissements')->where('slug', 'fde')->update([
            'nom' => "FACULTE DE DROIT ET D'ECONOMIE",
            'updated_at' => now(),
        ]);
    }
};
