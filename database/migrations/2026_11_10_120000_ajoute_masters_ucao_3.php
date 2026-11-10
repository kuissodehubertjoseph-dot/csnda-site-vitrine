<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const AJOUTS = ['Master 1 (ASSU)', 'Master 1 (ACG)', 'Master 1 (GRH)'];

    public function up(): void
    {
        $this->modifier(fn (array $c) => array_values(array_unique(array_merge($c, self::AJOUTS))));
    }

    public function down(): void
    {
        $this->modifier(fn (array $c) => array_values(array_diff($c, self::AJOUTS)));
    }

    private function modifier(callable $f): void
    {
        $e = DB::table('etablissements')->where('slug', 'ucao-3')->first();

        if (! $e) {
            return;
        }

        DB::table('etablissements')->where('id', $e->id)->update([
            'classes' => json_encode($f(json_decode($e->classes, true) ?? []), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }
};
