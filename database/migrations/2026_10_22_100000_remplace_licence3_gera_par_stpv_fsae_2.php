<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->remplacer('Licence 3 (GERA)', 'Licence 3 (STPV)');
    }

    public function down(): void
    {
        $this->remplacer('Licence 3 (STPV)', 'Licence 3 (GERA)');
    }

    private function remplacer(string $ancienne, string $nouvelle): void
    {
        foreach (DB::table('etablissements')->whereIn('slug', ['fsae', 'fsae-2'])->get() as $etablissement) {
            $this->remplacerPour($etablissement, $ancienne, $nouvelle);
        }
    }

    private function remplacerPour(object $etablissement, string $ancienne, string $nouvelle): void
    {

        $classes = array_map(
            fn ($classe) => $classe === $ancienne ? $nouvelle : $classe,
            json_decode($etablissement->classes, true) ?? []
        );

        DB::table('etablissements')
            ->where('id', $etablissement->id)
            ->update([
                'classes' => json_encode(array_values(array_unique($classes))),
                'updated_at' => now(),
            ]);

        DB::table('students')
            ->where('etablissement_id', $etablissement->id)
            ->where('classe', $ancienne)
            ->update(['classe' => $nouvelle, 'updated_at' => now()]);
    }
};
