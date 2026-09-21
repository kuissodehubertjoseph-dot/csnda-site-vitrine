<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Raccourcit le nom des filières EGEI au format "Licence N (SIGLE)" /
 * "Master N (SIGLE)", à la fois dans la liste des classes de l'établissement
 * et dans la classe déjà affectée aux élèves concernés (aucun à ce jour).
 */
return new class extends Migration
{
    private const CORRESPONDANCES = [
        'Licence 1 - Audit et Sécurité des Systèmes et Réseaux Informatiques' => 'Licence 1 (ASSRI)',
        'Licence 1 - Électronique' => 'Licence 1 (ELN)',
        'Licence 1 - Électrotechnique' => 'Licence 1 (ELT)',
        'Licence 2 - Électrotechnique' => 'Licence 2 (ELT)',
        'Licence 3 - Électrotechnique' => 'Licence 3 (ELT)',
        'Licence 1 - Génie Télécoms et TIC' => 'Licence 1 (GTT)',
        'Licence 2 - Génie Télécoms et TIC' => 'Licence 2 (GTT)',
        'Licence 3 - Génie Télécoms et TIC' => 'Licence 3 (GTT)',
        'Licence 1 - Informatique Industrielle et Maintenance' => 'Licence 1 (IIM)',
        'Licence 2 - Informatique Industrielle et Maintenance' => 'Licence 2 (IIM)',
        'Licence 3 - Informatique Industrielle et Maintenance' => 'Licence 3 (IIM)',
        'Licence 1 - Maintenance Biomédicale et Équipements Hospitaliers' => 'Licence 1 (MBEH)',
        'Licence 1 - Système Informatique et Logiciel' => 'Licence 1 (SIL)',
        'Master 2 - Télécommunications et Réseaux Informatiques' => 'Master 2 (TRI)',
        'Licence 1 - Agronomie' => 'Licence 1 (AGRO)',
        'Licence 2 - Agronomie' => 'Licence 2 (AGRO)',
        'Licence 3 - Production et Gestion des Ressources Animales' => 'Licence 3 (PGRA)',
        "Licence 1 - Gestion de l'Environnement et Aménagement du Territoire" => 'Licence 1 (GEAT)',
        "Licence 2 - Gestion de l'Environnement et Aménagement du Territoire" => 'Licence 2 (GEAT)',
        "Licence 3 - Gestion de l'Environnement et Aménagement du Territoire" => 'Licence 3 (GEAT)',
        'Licence 1 - Qualité Hygiène Sécurité Environnement' => 'Licence 1 (QHSE)',
        'Licence 1 - Tourisme' => 'Licence 1 (TOUR)',
        'Licence 1 - Droit' => 'Licence 1 (DRT)',
        'Licence 2 - Droit' => 'Licence 2 (DRT)',
        'Licence 3 - Droit' => 'Licence 3 (DRT)',
        'Licence 1 - Économie' => 'Licence 1 (ECO)',
        'Licence 2 - Économie' => 'Licence 2 (ECO)',
        'Licence 1 - Économétrie et Statistique Appliquée' => 'Licence 1 (ESA)',
        'Master 1 - Droit Privé Fondamental' => 'Master 1 (DPRF)',
        'Master 1 - Droit Public Fondamental' => 'Master 1 (DPUF)',
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
        $etablissement = DB::table('etablissements')->where('slug', 'egei')->first();

        if (! $etablissement) {
            return;
        }

        $classes = json_decode($etablissement->classes, true) ?? [];
        $classes = array_map(fn ($classe) => $correspondances[$classe] ?? $classe, $classes);

        DB::table('etablissements')
            ->where('slug', 'egei')
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
