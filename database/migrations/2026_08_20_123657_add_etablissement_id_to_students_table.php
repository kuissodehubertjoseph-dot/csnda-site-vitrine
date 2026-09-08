<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('etablissement_id')->nullable()->after('id')
                ->constrained('etablissements')->cascadeOnDelete();
        });

        // Les élèves déjà en base appartiennent tous au premier établissement
        // (CSS), créé par la migration précédente.
        $cssId = DB::table('etablissements')->where('slug', 'css')->value('id');

        if ($cssId) {
            DB::table('students')->update(['etablissement_id' => $cssId]);
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('etablissement_id');
        });
    }
};
