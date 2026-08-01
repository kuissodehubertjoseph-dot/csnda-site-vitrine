<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use App\Models\News;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $news = News::orderByDesc('published_at')->take(3)->get();
        $photos = GalleryPhoto::latest()->take(8)->get();
        $accroche = Setting::get('accueil_accroche', "Former des jeunes filles enracinées dans la foi, la discipline et l'excellence académique, au cœur de Cotonou.");

        return view('public.home', compact('news', 'photos', 'accroche'));
    }
}
