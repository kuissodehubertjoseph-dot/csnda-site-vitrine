<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable : un compte développeur n'appartient à aucun
            // établissement précis, il peut se connecter à n'importe lequel.
            $table->foreignId('etablissement_id')->nullable()->after('role')
                ->constrained('etablissements')->nullOnDelete();
        });

        $cssId = DB::table('etablissements')->where('slug', 'css')->value('id');

        if ($cssId) {
            DB::table('users')
                ->whereIn('role', ['dg', 'secretaire'])
                ->update(['etablissement_id' => $cssId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('etablissement_id');
        });
    }
};
