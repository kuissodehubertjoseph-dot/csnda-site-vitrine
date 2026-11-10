<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige les fautes de frappe de la liste de classes de Saint Jean-Baptiste.
 * Les élèves suivent le renommage de leur classe : aucun n'est déplacé.
 */
return new class extends Migration
{
    private const CORRESPONDANCES = [
        '3ère-E1' => '3ème-E1',
        '3ère-E3' => '3ème-E3',
        '3ère-E4' => '3ème-E4',
        '3ère-E5' => '3ème-E5',
        '3ère-E6' => '3ème-E6',
        '1D2' => '1ère-D2',
        'TD1' => 'Tle-D1',
        'TleA2' => 'Tle-A2',
        'TleB' => 'Tle-B',
        'TleC' => 'Tle-C',
    ];

    public function up(): void
    {
        $this->appliquer(self::CORRESPONDANCES);
    }

    public function down(): void
    {
        $this->appliquer(array_flip(self::CORRESPONDANCES));
    }

    private function appliquer(array $correspondances): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'jean-baptiste')->first();

        if (! $etablissement) {
            return;
        }

        DB::transaction(function () use ($etablissement, $correspondances) {
            $classes = json_decode($etablissement->classes, true) ?? [];

            DB::table('etablissements')->where('id', $etablissement->id)->update([
                'classes' => json_encode(array_map(fn ($c) => $correspondances[$c] ?? $c, $classes)),
                'updated_at' => now(),
            ]);

            foreach ($correspondances as $ancien => $nouveau) {
                DB::table('students')
                    ->where('etablissement_id', $etablissement->id)
                    ->where('classe', $ancien)
                    ->update(['classe' => $nouveau, 'updated_at' => now()]);
            }
        });
    }
};
