<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->modifier(function (array $classes) {
            if (in_array('2nde-D1', $classes, true)) {
                return $classes;
            }

            $position = array_search('2nde-D2', $classes, true);
            array_splice($classes, $position === false ? count($classes) : $position, 0, ['2nde-D1']);

            return $classes;
        });
    }

    public function down(): void
    {
        $this->modifier(fn (array $classes) => array_values(array_diff($classes, ['2nde-D1'])));
    }

    private function modifier(callable $transformation): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'jean-baptiste')->first();

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
