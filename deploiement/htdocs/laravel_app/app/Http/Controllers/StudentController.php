<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $recherche = $request->string('q')->trim()->toString();
        $classe = $request->string('classe')->trim()->toString();
        $statut = $request->string('statut')->trim()->toString();

        $eleves = Student::query()
            ->when($recherche !== '', function ($query) use ($recherche) {
                $query->where(function ($q) use ($recherche) {
                    $q->where('nom', 'like', "%{$recherche}%")
                        ->orWhere('prenoms', 'like', "%{$recherche}%")
                        ->orWhere('matricule', 'like', "%{$recherche}%");
                });
            })
            ->when($classe !== '', fn ($query) => $query->where('classe', $classe))
            ->when($statut !== '', fn ($query) => $query->where('statut', $statut))
            ->orderBy('classe')
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        return view('eleves.index', [
            'eleves' => $eleves,
            'classes' => config('ecole.classes'),
            'recherche' => $recherche,
            'classeSelectionnee' => $classe,
            'statutSelectionne' => $statut,
        ]);
    }

    public function create(): View
    {
        return view('eleves.create', [
            'classes' => config('ecole.classes'),
            'anneeScolaireCourante' => config('ecole.annee_scolaire_courante'),
        ]);
    }

    public function store(StudentStoreRequest $request): RedirectResponse
    {
        $donnees = $request->validated();

        if ($request->hasFile('photo')) {
            $donnees['photo'] = $request->file('photo')->store('eleves', 'public');
        }

        $donnees['statut'] = $donnees['statut'] ?? 'actif';

        $eleve = Student::create($donnees);

        return redirect()
            ->route('eleves.show', $eleve)
            ->with('succes', "Élève enregistré avec le matricule {$eleve->matricule}.");
    }

    public function show(Student $student): View
    {
        return view('eleves.show', ['eleve' => $student]);
    }

    public function edit(Student $student): View
    {
        return view('eleves.edit', [
            'eleve' => $student,
            'classes' => config('ecole.classes'),
        ]);
    }

    public function update(StudentUpdateRequest $request, Student $student): RedirectResponse
    {
        $donnees = $request->validated();

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $donnees['photo'] = $request->file('photo')->store('eleves', 'public');
        }

        $student->update($donnees);

        return redirect()
            ->route('eleves.show', $student)
            ->with('succes', 'Fiche élève mise à jour.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()
            ->route('eleves.index')
            ->with('succes', 'Élève supprimé.');
    }

    public function toggleStatut(Student $student): RedirectResponse
    {
        $student->update([
            'statut' => $student->statut === 'actif' ? 'inactif' : 'actif',
        ]);

        return back()->with('succes', "Statut de {$student->nom_complet} mis à jour.");
    }
}
