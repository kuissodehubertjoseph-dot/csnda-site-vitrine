<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute l'ensemble des filières Licence/Master de l'UCAO-ESMEA à la liste
 * des classes de l'établissement.
 */
return new class extends Migration
{
    private const FILIERES = [
        'Licence 2 - Action Commerciale et Force de Vente',
        'Licence 1 - Assurance',
        'Licence 2 - Assurance',
        'Licence 1 - Audit et Contrôle de Gestion',
        'Licence 2 - Audit et Contrôle de Gestion',
        'Licence 3 - Audit et Contrôle de Gestion',
        "Licence 1 - Banque et Finance d'Entreprise",
        "Licence 2 - Banque et Finance d'Entreprise",
        "Licence 3 - Banque et Finance d'Entreprise",
        'Licence 1 - Commerce',
        'Licence 2 - Commerce',
        'Licence 3 - Commerce',
        'Licence 1 - Communication et Action Publicitaire',
        'Licence 2 - Communication et Action Publicitaire',
        'Licence 3 - Communication et Action Publicitaire',
        'Licence 1 - Informatique de Gestion',
        'Licence 2 - Informatique de Gestion',
        'Licence 3 - Informatique de Gestion',
        'Licence 1 - Management des Ressources Humaines',
        'Licence 2 - Management des Ressources Humaines',
        'Licence 3 - Management des Ressources Humaines',
        'Licence 1 - Transport et Logistique',
        'Licence 2 - Transport et Logistique',
        'Licence 3 - Transport et Logistique',
        'Master 1 - Commerce International',
        'Master 1 - Marketing et Communication',
        'Master 1 - Transport et Logistique',
    ];

    public function up(): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'ucao')->first();

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
            ->where('slug', 'ucao')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $etablissement = DB::table('etablissements')->where('slug', 'ucao')->first();

        if (! $etablissement) {
            return;
        }

        $classes = json_decode($etablissement->classes, true) ?? [];
        $classes = array_values(array_diff($classes, self::FILIERES));

        DB::table('etablissements')
            ->where('slug', 'ucao')
            ->update([
                'classes' => json_encode($classes),
                'updated_at' => now(),
            ]);
    }
};
