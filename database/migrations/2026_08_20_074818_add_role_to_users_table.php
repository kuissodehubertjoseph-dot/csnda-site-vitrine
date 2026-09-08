<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(User::ROLE_SECRETAIRE)->after('email');
        });

        // Les comptes déjà en place sont ceux de la direction : on les passe en DG
        // pour ne pas leur retirer d'accès au moment de la migration.
        DB::table('users')->update(['role' => User::ROLE_DG]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
