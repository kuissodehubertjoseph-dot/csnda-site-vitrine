<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un import PDF ne fournit que nom/prénoms/matricule/classe : ces champs
     * doivent pouvoir rester vides jusqu'à complétion manuelle de la fiche.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->date('date_naissance')->nullable()->change();
            $table->string('lieu_naissance')->nullable()->change();
            $table->enum('sexe', ['M', 'F'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->date('date_naissance')->nullable(false)->change();
            $table->string('lieu_naissance')->nullable(false)->change();
            $table->enum('sexe', ['M', 'F'])->nullable(false)->change();
        });
    }
};
