<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Remplace la liste de classes provisoire de Saint Jean-Baptiste par la
 * liste réelle communiquée par l'établissement.
 */
return new class extends Migration
{
    private const ANCIENNES_CLASSES = [
        'CI', 'CP', 'CE1', 'CE2', 'CM1', 'CM2',
        '6ème', '5ème', '4ème', '3ème',
        '2nde A', '2nde C',
        '1ère A', '1ère D',
        'Tle A', 'Tle D',
    ];

    private const NOUVELLES_CLASSES = [
        '1ère-A2', '1ère-B', '1ère-C', '1ère-D1', '1D2', '1ère-F4', '1ère-G', '1ère-MMV',
        '2nde-AB', '2nde-C', '2nde-D2', '2nde-F3', '2nde-F4', '2nde-G2', '2nde-HR', '2nde-MMV',
        '3ère-E1', '3ème-E2', '3ère-E3', '3ère-E4', '3ère-E5', '3ère-E6',
        '4ème-E1', '4ème-E2', '4ème-E3', '4ème-E4', '4ème-E5',
        '5ème-E1', '5ème-E2', '5ème-E3', '5ème-E4', '5ème-E5',
        '6ème-E1', '6ème-E2', '6ème-E3', '6ème-E4', '6ème-E5',
        'TleA2', 'TleB', 'TleC', 'TD1', 'Tle-D2', 'Tle-F4', 'Tle-G', 'Tle-MMV',
    ];

    public function up(): void
    {
        $this->remplacer(self::NOUVELLES_CLASSES);
    }

    public function down(): void
    {
        $this->remplacer(self::ANCIENNES_CLASSES);
    }

    private function remplacer(array $classes): void
    {
        DB::table('etablissements')
            ->where('slug', 'jean-baptiste')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);
    }
};
