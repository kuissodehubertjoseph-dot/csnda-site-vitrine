<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory;

class GalleryController extends Controller
{
    public function index()
    {
        $categories = GalleryCategory::with('photos')->get();

        return view('public.gallery', compact('categories'));
    }
}
