<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Active les cartes des écoles UCAO 3, FDE 3, EGEI 3 et FSAE 3. */
return new class extends Migration
{
    private const SLUGS = ['ucao-3', 'fde-3', 'egei-3', 'fsae-3'];

    public function up(): void
    {
        DB::table('etablissements')->whereIn('slug', self::SLUGS)->update(['statut' => 'actif']);
    }

    public function down(): void
    {
        DB::table('etablissements')->whereIn('slug', self::SLUGS)->update(['statut' => 'bientot']);
    }
};
