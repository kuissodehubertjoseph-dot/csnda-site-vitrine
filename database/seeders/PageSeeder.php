<?php

namespace Database\Seeders;

use App\Models\Page;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(['slug' => 'presentation'], [
            'title' => 'Présentation',
            'content' => "Le Cours Secondaire Notre-Dame des Apôtres (CSNDA) est un établissement d'enseignement catholique situé à Cotonou, au Bénin. Depuis sa création, notre école accueille des jeunes filles du Cours Primaire à la Terminale, dans un cadre exigeant qui allie rigueur académique, encadrement moral et formation spirituelle.\n\nNotre projet éducatif repose sur trois piliers : la Prière, la Discipline et le Travail, résumés par notre devise \"Optimus esse aut non esse\" — Être excellent ou ne pas être.",
            'image' => PlaceholderImage::make('pages', 'Présentation CSNDA'),
        ]);

        Page::updateOrCreate(['slug' => 'historique'], [
            'title' => 'Historique',
            'content' => "Fondé par la Congrégation des Sœurs Notre-Dame des Apôtres, le CSNDA s'est imposé au fil des années comme une référence de l'enseignement secondaire féminin à Cotonou.\n\nDe ses premières classes à son développement actuel couvrant tout le cycle secondaire, l'école n'a cessé de perfectionner son encadrement pédagogique tout en restant fidèle à ses valeurs fondatrices.",
        ]);

        Page::updateOrCreate(['slug' => 'mot-directeur'], [
            'title' => 'Mot du directeur',
            'content' => "Chers parents, chers élèves,\n\nC'est avec beaucoup de fierté que je vous accueille au Cours Secondaire Notre-Dame des Apôtres. Notre équipe éducative s'engage chaque jour à former des jeunes filles épanouies, disciplinées et tournées vers l'excellence.\n\nEnsemble, faisons de la réussite de chaque élève notre priorité commune.",
            'image' => PlaceholderImage::make('pages', 'Le Directeur'),
        ]);
    }
}
