<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderByDesc('published_at')->paginate(15);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        News::create($data);

        return redirect()->route('admin.actualites.index')->with('success', "L'actualité a été créée.");
    }

    public function edit(News $actualite)
    {
        return view('admin.news.edit', ['news' => $actualite]);
    }

    public function update(Request $request, News $actualite): RedirectResponse
    {
        $data = $this->validated($request);

        if ($data['title'] !== $actualite->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $actualite->id);
        }

        if ($request->hasFile('image')) {
            if ($actualite->image) {
                Storage::disk('public')->delete($actualite->image);
            }
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $actualite->update($data);

        return redirect()->route('admin.actualites.index')->with('success', "L'actualité a été mise à jour.");
    }

    public function destroy(News $actualite): RedirectResponse
    {
        if ($actualite->image) {
            Storage::disk('public')->delete($actualite->image);
        }

        $actualite->delete();

        return redirect()->route('admin.actualites.index')->with('success', "L'actualité a été supprimée.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'published_at' => ['required', 'date'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (News::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
