<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PresentationController extends Controller
{
    public function index()
    {
        $presentation = Page::firstOrNew(['slug' => 'presentation'], ['title' => 'Présentation']);
        $historique = Page::firstOrNew(['slug' => 'historique'], ['title' => 'Historique']);
        $motDirecteur = Page::firstOrNew(['slug' => 'mot-directeur'], ['title' => 'Mot du directeur']);

        return view('public.presentation', compact('presentation', 'historique', 'motDirecteur'));
    }
}
