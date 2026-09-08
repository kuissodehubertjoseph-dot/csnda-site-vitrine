<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('etablissements', function (Blueprint $table) {
            $table->string('siege_social')->nullable()->after('adresse');
            $table->string('telephone_mobile')->nullable()->after('telephone_secretariat');
            $table->string('email')->nullable()->after('telephone_mobile');
            $table->string('site_web')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etablissements', function (Blueprint $table) {
            $table->dropColumn(['siege_social', 'telephone_mobile', 'email', 'site_web']);
        });
    }
};
