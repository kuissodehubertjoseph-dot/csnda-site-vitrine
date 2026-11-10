<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->modifier(function (array $c) {
            if (! in_array('Master 1 (SIL)', $c, true)) {
                $c[] = 'Master 1 (SIL)';
            }

            return $c;
        });
    }

    public function down(): void
    {
        $this->modifier(fn (array $c) => array_values(array_diff($c, ['Master 1 (SIL)'])));
    }

    private function modifier(callable $f): void
    {
        $e = DB::table('etablissements')->where('slug', 'egei-3')->first();

        if (! $e) {
            return;
        }

        DB::table('etablissements')->where('id', $e->id)->update([
            'classes' => json_encode($f(json_decode($e->classes, true) ?? []), JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }
};
