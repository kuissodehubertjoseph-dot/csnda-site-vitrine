<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryCategory;
use App\Models\GalleryPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryPhotoController extends Controller
{
    public function index()
    {
        $photos = GalleryPhoto::with('category')->latest()->paginate(18);

        return view('admin.gallery-photos.index', compact('photos'));
    }

    public function create()
    {
        $categories = GalleryCategory::orderBy('name')->get();

        return view('admin.gallery-photos.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'gallery_category_id' => ['nullable', 'exists:gallery_categories,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $data['image'] = $request->file('image')->store('gallery', 'public');

        GalleryPhoto::create($data);

        return redirect()->route('admin.photos-galerie.index')->with('success', 'Photo ajoutée à la galerie.');
    }

    public function destroy(GalleryPhoto $photosGalerie): RedirectResponse
    {
        Storage::disk('public')->delete($photosGalerie->image);
        $photosGalerie->delete();

        return redirect()->route('admin.photos-galerie.index')->with('success', 'Photo supprimée.');
    }
}
