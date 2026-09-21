<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute l'ensemble des filières d'EGEI à la liste des classes de
 * l'établissement.
 */
return new class extends Migration
{
    private const FILIERES = [
        'Licence 1 - Audit et Sécurité des Systèmes et Réseaux Informatiques',
        'Licence 1 - Électronique',
        'Licence 1 - Électrotechnique',
        'Licence 2 - Électrotechnique',
        'Licence 3 - Électrotechnique',
        'Licence 1 - Génie Télécoms et TIC',
        'Licence 2 - Génie Télécoms et TIC',
        'Licence 3 - Génie Télécoms et TIC',
        'Licence 1 - Informatique Industrielle et Maintenance',
        'Licence 2 - Informatique Industrielle et Maintenance',
        'Licence 3 - Informatique Industrielle et Maintenance',
        'Licence 1 - Maintenance Biomédicale et Équipements Hospitaliers',
        'Licence 1 - Système Informatique et Logiciel',
        'Master 2 - Télécommunications et Réseaux Informatiques',
        'Licence 1 - Agronomie',
        'Licence 2 - Agronomie',
        'Licence 3 - Production et Gestion des Ressources Animales',
        "Licence 1 - Gestion de l'Environnement et Aménagement du Territoire",
        "Licence 2 - Gestion de l'Environnement et Aménagement du Territoire",
        "Licence 3 - Gestion de l'Environnement et Aménagement du Territoire",
        'Licence 1 - Qualité Hygiène Sécurité Environnement',
        'Licence 1 - Tourisme',
        'Licence 1 - Droit',
        'Licence 2 - Droit',
        'Licence 3 - Droit',
        'Licence 1 - Économie',
        'Licence 2 - Économie',
        'Licence 1 - Économétrie et Statistique Appliquée',
        'Master 1 - Droit Privé Fondamental',
        'Master 1 - Droit Public Fondamental',
    ];

    public function up(): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'egei')->first();

        if (! $etablissement) {
            return;
        }

        $classes = json_decode($etablissement->classes, true) ?? [];

        foreach (self::FILIERES as $filiere) {
            if (! in_array($filiere, $classes, true)) {
                $classes[] = $filiere;
            }
        }

        DB::table('etablissements')
            ->where('slug', 'egei')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'egei')->first();

        if (! $etablissement) {
            return;
        }

        $classes = json_decode($etablissement->classes, true) ?? [];
        $classes = array_values(array_diff($classes, self::FILIERES));

        DB::table('etablissements')
            ->where('slug', 'egei')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);
    }
};
