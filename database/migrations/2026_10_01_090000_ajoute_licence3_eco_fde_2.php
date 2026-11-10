<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->modifier(function (array $classes) {
            if (in_array('Licence 3 (ECO)', $classes, true)) {
                return $classes;
            }

            $position = array_search('Licence 2 (ECO)', $classes, true);
            array_splice($classes, $position === false ? count($classes) : $position + 1, 0, ['Licence 3 (ECO)']);

            return $classes;
        });
    }

    public function down(): void
    {
        $this->modifier(fn (array $classes) => array_values(array_diff($classes, ['Licence 3 (ECO)'])));
    }

    private function modifier(callable $transformation): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'fde-2')->first();

        if (! $etablissement) {
            return;
        }

        DB::table('etablissements')
            ->where('id', $etablissement->id)
            ->update([
                'classes' => json_encode($transformation(json_decode($etablissement->classes, true) ?? [])),
                'updated_at' => now(),
            ]);
    }
};
