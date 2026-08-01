<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderByDesc('published_at')->paginate(9);

        return view('public.news.index', compact('news'));
    }

    public function show(News $news)
    {
        $recent = News::where('id', '!=', $news->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('public.news.show', compact('news', 'recent'));
    }
}
