<?php

namespace Database\Seeders;

use App\Models\Filiere;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        $filieres = [
            ['name' => 'Cours Primaire', 'cycle' => 'Primaire', 'description' => "Apprentissages fondamentaux dans un cadre bienveillant, de la maternelle au CM2."],
            ['name' => '6ème', 'cycle' => 'Premier cycle', 'description' => "Classe d'entrée en secondaire, consolidation des bases."],
            ['name' => '5ème', 'cycle' => 'Premier cycle', 'description' => "Poursuite du tronc commun avec approfondissement des matières scientifiques et littéraires."],
            ['name' => '4ème', 'cycle' => 'Premier cycle', 'description' => "Préparation progressive à l'orientation vers le second cycle."],
            ['name' => '3ème', 'cycle' => 'Premier cycle', 'description' => "Classe du Brevet d'Études du Premier Cycle (BEPC)."],
            ['name' => '2nde', 'cycle' => 'Second cycle', 'description' => "Entrée au lycée avec choix de séries scientifiques ou littéraires."],
            ['name' => '1ère', 'cycle' => 'Second cycle', 'description' => "Classe du probatoire, approfondissement des séries choisies."],
            ['name' => 'Terminale (Tle)', 'cycle' => 'Second cycle', 'description' => "Classe du Baccalauréat, préparation intensive aux examens et à l'enseignement supérieur."],
        ];

        foreach ($filieres as $order => $filiere) {
            Filiere::updateOrCreate(
                ['name' => $filiere['name']],
                $filiere + ['order' => $order]
            );
        }
    }
}
