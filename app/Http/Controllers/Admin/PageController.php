<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('image')) {
            if ($page->image) {
                Storage::disk('public')->delete($page->image);
            }
            $data['image'] = $request->file('image')->store('pages', 'public');
        }

        $page->update($data);

        return back()->with('success', 'Le contenu de la page a été mis à jour.');
    }
}
