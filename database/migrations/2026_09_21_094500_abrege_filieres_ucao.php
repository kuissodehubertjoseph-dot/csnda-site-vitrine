<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Raccourcit le nom des filières UCAO au format "Licence N (SIGLE)" /
 * "Master N (SIGLE)", à la fois dans la liste des classes de l'établissement
 * et dans la classe déjà affectée aux élèves concernés.
 */
return new class extends Migration
{
    private const CORRESPONDANCES = [
        'Licence 1 - Action Commerciale et Force de Vente' => 'Licence 1 (ACFV)',
        'Licence 2 - Action Commerciale et Force de Vente' => 'Licence 2 (ACFV)',
        'Licence 1 - Assurance' => 'Licence 1 (ASSU)',
        'Licence 2 - Assurance' => 'Licence 2 (ASSU)',
        'Licence 1 - Audit et Contrôle de Gestion' => 'Licence 1 (ACG)',
        'Licence 2 - Audit et Contrôle de Gestion' => 'Licence 2 (ACG)',
        'Licence 3 - Audit et Contrôle de Gestion' => 'Licence 3 (ACG)',
        "Licence 1 - Banque et Finance d'Entreprise" => 'Licence 1 (BFE)',
        "Licence 2 - Banque et Finance d'Entreprise" => 'Licence 2 (BFE)',
        "Licence 3 - Banque et Finance d'Entreprise" => 'Licence 3 (BFE)',
        'Licence 1 - Commerce' => 'Licence 1 (COM)',
        'Licence 2 - Commerce' => 'Licence 2 (COM)',
        'Licence 3 - Commerce' => 'Licence 3 (COM)',
        'Licence 1 - Communication et Action Publicitaire' => 'Licence 1 (CAP)',
        'Licence 2 - Communication et Action Publicitaire' => 'Licence 2 (CAP)',
        'Licence 3 - Communication et Action Publicitaire' => 'Licence 3 (CAP)',
        'Licence 1 - Informatique de Gestion' => 'Licence 1 (IG)',
        'Licence 2 - Informatique de Gestion' => 'Licence 2 (IG)',
        'Licence 3 - Informatique de Gestion' => 'Licence 3 (IG)',
        'Licence 1 - Management des Ressources Humaines' => 'Licence 1 (MRH)',
        'Licence 2 - Management des Ressources Humaines' => 'Licence 2 (MRH)',
        'Licence 3 - Management des Ressources Humaines' => 'Licence 3 (MRH)',
        'Licence 1 - Transport et Logistique' => 'Licence 1 (TL)',
        'Licence 2 - Transport et Logistique' => 'Licence 2 (TL)',
        'Licence 3 - Transport et Logistique' => 'Licence 3 (TL)',
        'Master 1 - Commerce International' => 'Master 1 (CI)',
        'Master 1 - Marketing et Communication' => 'Master 1 (MC)',
        'Master 1 - Transport et Logistique' => 'Master 1 (TL)',
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
        $etablissement = DB::table('etablissements')->where('slug', 'ucao')->first();

        if (! $etablissement) {
            return;
        }

        $classes = json_decode($etablissement->classes, true) ?? [];
        $classes = array_map(fn ($classe) => $correspondances[$classe] ?? $classe, $classes);

        DB::table('etablissements')
            ->where('slug', 'ucao')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);

        foreach ($correspondances as $ancien => $nouveau) {
            DB::table('students')
                ->where('etablissement_id', $etablissement->id)
                ->where('classe', $ancien)
                ->update(['classe' => $nouveau, 'updated_at' => now()]);
        }
    }
};
