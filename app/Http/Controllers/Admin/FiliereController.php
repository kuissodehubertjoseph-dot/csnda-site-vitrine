<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    public function index()
    {
        $filieres = Filiere::orderBy('order')->orderBy('id')->get();

        return view('admin.filieres.index', compact('filieres'));
    }

    public function create()
    {
        return view('admin.filieres.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Filiere::create($data);

        return redirect()->route('admin.filieres.index')->with('success', 'Filière ajoutée.');
    }

    public function edit(Filiere $filiere)
    {
        return view('admin.filieres.edit', compact('filiere'));
    }

    public function update(Request $request, Filiere $filiere): RedirectResponse
    {
        $filiere->update($this->validated($request));

        return redirect()->route('admin.filieres.index')->with('success', 'Filière mise à jour.');
    }

    public function destroy(Filiere $filiere): RedirectResponse
    {
        $filiere->delete();

        return redirect()->route('admin.filieres.index')->with('success', 'Filière supprimée.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cycle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
