<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryCategoryController extends Controller
{
    public function index()
    {
        $categories = GalleryCategory::withCount('photos')->orderBy('name')->get();

        return view('admin.gallery-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.gallery-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        GalleryCategory::create($data);

        return redirect()->route('admin.categories-galerie.index')->with('success', 'Catégorie créée.');
    }

    public function edit(GalleryCategory $categoriesGalerie)
    {
        return view('admin.gallery-categories.edit', ['category' => $categoriesGalerie]);
    }

    public function update(Request $request, GalleryCategory $categoriesGalerie): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        $categoriesGalerie->update($data);

        return redirect()->route('admin.categories-galerie.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(GalleryCategory $categoriesGalerie): RedirectResponse
    {
        $categoriesGalerie->delete();

        return redirect()->route('admin.categories-galerie.index')->with('success', 'Catégorie supprimée.');
    }
}
