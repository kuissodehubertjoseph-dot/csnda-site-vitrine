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

        $pdf = $this->navigateurCarte($html)->pdf();

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
     * Génère deux PDF multi-pages pour tout le lot — un avec tous les rectos,
     * un avec tous les versos — plutôt qu'un fichier unique mêlant les deux
     * faces : facilite l'impression recto/verso à la chaîne (un fichier par
     * bac de la chargeuse).
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

        // La génération PDF (Chromium + encodage base64 des pages) est gourmande en
        // mémoire ; on l'augmente pour cette seule requête plutôt que globalement.
        ini_set('memory_limit', '1024M');
        // Un seul PDF par face pour tout le lot : peut prendre plusieurs minutes
        // pour une grande classe.
        set_time_limit(0);

        $lot = Str::uuid()->toString();

        Storage::disk('local')->makeDirectory('exports');

        foreach (['recto', 'verso'] as $face) {
            $html = view('cartes.lot-face', ['eleves' => $eleves, 'face' => $face])->render();
            // Écrit directement sur disque (save) plutôt que de récupérer le PDF
            // en mémoire PHP : un lot complet peut peser plusieurs dizaines de Mo.
            $this->navigateurCarteLot($html, $eleves->count())
                ->save(Storage::disk('local')->path("exports/{$lot}-0-{$face}.pdf"));
            unset($html);
        }

        return view('cartes.lot-resultat', [
            'lot' => $lot,
            'nombreEleves' => $eleves->count(),
        ]);
    }

    /**
     * Téléchargement d'un des PDF (recto ou verso) générés lors d'une impression par lot.
     */
    public function telechargerLot(string $lot, string $face, int $paquet = 0): Response
    {
        abort_unless(preg_match('/^[a-f0-9\-]{36}$/i', $lot) === 1, 404);
        abort_unless(in_array($face, ['recto', 'verso'], true), 404);

        $chemin = "exports/{$lot}-{$paquet}-{$face}.pdf";

        abort_unless(Storage::disk('local')->exists($chemin), 404);

        return response(Storage::disk('local')->get($chemin), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"cartes-{$face}s.pdf\"",
        ]);
    }

    /**
     * Instance Browsershot pré-configurée pour un rendu net (haute résolution) au format CR80.
     */
    private function navigateurCarte(string $html): Browsershot
    {
        return Browsershot::html($html)
            ->setNodeModulePath(base_path('node_modules'))
            ->noSandbox()
            ->showBackground()
            ->paperSize(85.6, 55, 'mm')
            ->margins(0, 0, 0, 0)
            ->deviceScaleFactor(4)
            ->timeout(60);
    }

    /**
     * Variante utilisée pour l'impression par lot : échelle réduite (toujours nette
     * à l'impression) pour limiter la mémoire et la taille des PDF multi-pages,
     * et un délai proportionnel au nombre de cartes (tout le lot tient dans un seul PDF).
     */
    private function navigateurCarteLot(string $html, int $nombreCartes): Browsershot
    {
        return Browsershot::html($html)
            ->setNodeModulePath(base_path('node_modules'))
            ->noSandbox()
            ->showBackground()
            ->paperSize(85.6, 55, 'mm')
            ->margins(0, 0, 0, 0)
            ->deviceScaleFactor(2)
            ->timeout(max(180, $nombreCartes * 4));
    }
}
