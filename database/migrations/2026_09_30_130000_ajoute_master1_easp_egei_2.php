<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->modifier(function (array $classes) {
            if (in_array('Master 1 (EASP)', $classes, true)) {
                return $classes;
            }

            $position = array_search('Master 2 (TRI)', $classes, true);
            array_splice($classes, $position === false ? count($classes) : $position, 0, ['Master 1 (EASP)']);

            return $classes;
        });
    }

    public function down(): void
    {
        $this->modifier(fn (array $classes) => array_values(array_diff($classes, ['Master 1 (EASP)'])));
    }

    private function modifier(callable $transformation): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'egei-2')->first();

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
