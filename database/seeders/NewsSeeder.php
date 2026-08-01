<?php

namespace Database\Seeders;

use App\Models\News;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Rentrée scolaire 2026-2027',
                'excerpt' => "La rentrée des classes aura lieu le premier lundi de septembre. Retrouvez toutes les informations pratiques.",
                'content' => "Le Cours Secondaire Notre-Dame des Apôtres accueillera ses élèves pour la nouvelle année scolaire dès le premier lundi de septembre. Les inscriptions et réinscriptions sont ouvertes au secrétariat de l'école.\n\nNous invitons les parents à se munir des documents administratifs nécessaires ainsi que du dernier bulletin de notes.",
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Journée culturelle et sportive du CSNDA',
                'excerpt' => "Une journée haute en couleurs consacrée aux talents artistiques et sportifs de nos élèves.",
                'content' => "Le CSNDA a organisé sa traditionnelle journée culturelle et sportive, rassemblant élèves, enseignants et parents autour d'activités variées : chants, danses traditionnelles, athlétisme et jeux collectifs.\n\nCet événement annuel renforce la cohésion et met en valeur les talents multiples de nos élèves.",
                'published_at' => now()->subDays(15),
            ],
            [
                'title' => 'Résultats exceptionnels au Baccalauréat',
                'excerpt' => "Le CSNDA félicite ses lauréates pour leurs excellents résultats à la session du Baccalauréat.",
                'content' => "Nous sommes fiers d'annoncer un taux de réussite remarquable de nos élèves de Terminale à la dernière session du Baccalauréat. Ce résultat couronne les efforts conjugués des élèves, de leurs familles et de toute l'équipe pédagogique.\n\nToutes nos félicitations aux lauréates et bon vent pour la suite de leur parcours académique.",
                'published_at' => now()->subDays(40),
            ],
            [
                'title' => 'Retraite spirituelle des élèves de Terminale',
                'excerpt' => "Un temps de recueillement et de réflexion pour les élèves en fin de cycle secondaire.",
                'content' => "Dans le cadre de la formation spirituelle proposée par l'établissement, les élèves de Terminale ont pris part à une retraite spirituelle animée par l'aumônerie de l'école. Ce moment fort a permis à chacune de se recentrer avant les examens de fin d'année.",
                'published_at' => now()->subDays(60),
            ],
        ];

        foreach ($articles as $article) {
            News::updateOrCreate(
                ['slug' => Str::slug($article['title'])],
                $article + [
                    'slug' => Str::slug($article['title']),
                    'image' => PlaceholderImage::make('news', 'Actualité CSNDA'),
                ]
            );
        }
    }
}
