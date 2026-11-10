<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->modifier(function (array $classes) {
            if (! in_array('Master 2 (PJ)', $classes, true)) {
                $classes[] = 'Master 2 (PJ)';
            }

            return $classes;
        });
    }

    public function down(): void
    {
        $this->modifier(fn (array $classes) => array_values(array_diff($classes, ['Master 2 (PJ)'])));
    }

    private function modifier(callable $transformation): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'fde-3')->first();

        if (! $etablissement) {
            return;
        }

        DB::table('etablissements')
            ->where('id', $etablissement->id)
            ->update([
                'classes' => json_encode($transformation(json_decode($etablissement->classes, true) ?? []), JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
    }
};
