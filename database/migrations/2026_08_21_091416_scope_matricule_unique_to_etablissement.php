<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * La contrainte d'unicité sur "matricule" était globale (héritée d'avant
     * le multi-établissement) : deux écoles différentes ne pouvaient pas
     * avoir chacune un élève portant le même matricule, alors que la
     * validation applicative (StudentStoreRequest/StudentUpdateRequest) est
     * déjà scoped par etablissement_id — d'où un 500 (UniqueConstraint) côté
     * base alors que le formulaire ne signalait aucune erreur. On remplace
     * l'unicité globale par une unicité (etablissement_id, matricule).
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_matricule_unique');
            $table->unique(['etablissement_id', 'matricule']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['etablissement_id', 'matricule']);
            $table->unique('matricule');
        });
    }
};
