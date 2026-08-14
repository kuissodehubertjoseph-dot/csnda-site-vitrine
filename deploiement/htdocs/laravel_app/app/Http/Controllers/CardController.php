<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;

class CardController extends Controller
{
    /**
     * Aperçu HTML brut du recto (utilisé en iframe dans la fiche élève).
     */
    public function recto(Student $student): View
    {
        return view('cartes.recto', ['eleve' => $student]);
    }

    /**
     * Aperçu HTML brut du verso (utilisé en iframe dans la fiche élève).
     */
    public function verso(Student $student): View
    {
        return view('cartes.verso', ['eleve' => $student]);
    }

    /**
     * Génère et télécharge la carte complète (recto + verso, 2 pages) d'un élève, au format CR80.
     */
    public function telecharger(Student $student): Response
    {
        $html = view('cartes.complete', ['eleve' => $student])->render();

        $pdf = $this->navigateurCarte()
            ->html($html)
            ->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="carte-'.$student->matricule.'.pdf"',
        ]);
    }

    /**
     * Formulaire de sélection pour l'impression par lot (par classe et/ou élèves cochés).
     */
    public function formulaireLot(Request $request): View
    {
        $classe = $request->string('classe')->trim()->toString();

        $eleves = Student::query()
            ->where('statut', 'actif')
            ->when($classe !== '', fn ($query) => $query->where('classe', $classe))
            ->orderBy('classe')
            ->orderBy('nom')
            ->get();

        return view('cartes.lot-form', [
            'eleves' => $eleves,
            'classes' => config('ecole.classes'),
            'classeSelectionnee' => $classe,
        ]);
    }

    /**
     * Génère deux PDF multi-pages (tous les rectos, puis tous les versos) pour la sélection.
     */
    public function genererLot(Request $request): View|RedirectResponse
    {
        $request->validate([
            'classe' => ['nullable', 'string'],
            'eleves' => ['nullable', 'array'],
            'eleves.*' => ['integer', 'exists:students,id'],
        ]);

        $idsSelectionnes = $request->input('eleves', []);

        $eleves = ! empty($idsSelectionnes)
            ? Student::whereIn('id', $idsSelectionnes)->orderBy('classe')->orderBy('nom')->get()
            : Student::where('classe', $request->string('classe')->toString())->orderBy('nom')->get();

        if ($eleves->isEmpty()) {
            return back()->withErrors(['eleves' => "Sélectionnez au moins un élève ou une classe avant de générer les cartes."]);
        }

        $lot = Str::uuid()->toString();

        Storage::disk('local')->makeDirectory('exports');

        $pdfRecto = $this->navigateurCarte()
            ->html(view('cartes.lot-recto', ['eleves' => $eleves])->render())
            ->pdf();

        $pdfVerso = $this->navigateurCarte()
            ->html(view('cartes.lot-verso', ['eleves' => $eleves])->render())
            ->pdf();

        Storage::disk('local')->put("exports/{$lot}-recto.pdf", $pdfRecto);
        Storage::disk('local')->put("exports/{$lot}-verso.pdf", $pdfVerso);

        return view('cartes.lot-resultat', [
            'lot' => $lot,
            'nombreEleves' => $eleves->count(),
        ]);
    }

    /**
     * Téléchargement d'un des deux PDF générés lors d'une impression par lot.
     */
    public function telechargerLot(string $lot, string $type): Response
    {
        abort_unless(in_array($type, ['recto', 'verso'], true), 404);
        abort_unless(preg_match('/^[a-f0-9\-]{36}$/i', $lot) === 1, 404);

        $chemin = "exports/{$lot}-{$type}.pdf";

        abort_unless(Storage::disk('local')->exists($chemin), 404);

        return response(Storage::disk('local')->get($chemin), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"cartes-{$type}.pdf\"",
        ]);
    }

    /**
     * Instance Browsershot pré-configurée pour un rendu net (haute résolution) au format CR80.
     */
    private function navigateurCarte(): Browsershot
    {
        return Browsershot::html('')
            ->setNodeModulePath(base_path('node_modules'))
            ->noSandbox()
            ->showBackground()
            ->paperSize(85.6, 54, 'mm')
            ->margins(0, 0, 0, 0)
            ->deviceScaleFactor(4)
            ->timeout(60);
    }
}
