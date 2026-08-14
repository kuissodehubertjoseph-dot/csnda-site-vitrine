<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Smalot\PdfParser\Parser as PdfParser;

class StudentImportController extends Controller
{
    /**
     * Formulaire de dépôt du fichier PDF (liste d'élèves de l'école).
     */
    public function create(): View
    {
        return view('eleves.import.form', [
            'classes' => config('ecole.classes'),
            'anneeScolaireCourante' => config('ecole.annee_scolaire_courante'),
        ]);
    }

    /**
     * Extrait les lignes du PDF et affiche un tableau modifiable
     * avant tout enregistrement en base.
     */
    public function apercu(Request $request): View|RedirectResponse
    {
        $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'classe' => ['required', 'string'],
            'annee_scolaire' => ['required', 'string', 'max:20'],
        ]);

        $texte = (new PdfParser())->parseFile($request->file('pdf')->getRealPath())->getText();

        $lignes = $this->extraireEleves($texte);

        if (empty($lignes)) {
            return back()
                ->withInput()
                ->with('erreur', "Aucune ligne exploitable n'a été trouvée dans ce PDF. Le fichier est peut-être un scan (image) plutôt qu'un PDF texte : dans ce cas, la saisie doit se faire manuellement.");
        }

        return view('eleves.import.apercu', [
            'lignes' => $lignes,
            'classe' => $request->string('classe')->toString(),
            'anneeScolaire' => $request->string('annee_scolaire')->toString(),
        ]);
    }

    /**
     * Enregistre les lignes validées par l'utilisateur sur l'écran d'aperçu.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'classe' => ['required', 'string'],
            'annee_scolaire' => ['required', 'string', 'max:20'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.inclure' => ['nullable', 'boolean'],
            'lignes.*.nom' => ['required_with:lignes.*.inclure', 'nullable', 'string', 'max:100'],
            'lignes.*.prenoms' => ['nullable', 'string', 'max:150'],
            'lignes.*.matricule' => ['nullable', 'string', 'max:50'],
        ]);

        $cree = 0;
        $ignores = 0;

        foreach ($data['lignes'] as $ligne) {
            if (empty($ligne['inclure']) || empty($ligne['nom'])) {
                continue;
            }

            $matricule = trim((string) ($ligne['matricule'] ?? ''));

            if ($matricule !== '' && Student::where('matricule', $matricule)->exists()) {
                $ignores++;

                continue;
            }

            Student::create([
                'matricule' => $matricule !== '' ? $matricule : null,
                'nom' => trim($ligne['nom']),
                'prenoms' => trim((string) ($ligne['prenoms'] ?? '')),
                'classe' => $data['classe'],
                'annee_scolaire' => $data['annee_scolaire'],
                'statut' => 'actif',
            ]);

            $cree++;
        }

        $message = "{$cree} élève(s) importé(s).";
        if ($ignores > 0) {
            $message .= " {$ignores} ligne(s) ignorée(s) (matricule déjà existant).";
        }
        $message .= ' Pensez à compléter les fiches (date de naissance, sexe, photo...).';

        return redirect()
            ->route('eleves.index', ['classe' => $data['classe']])
            ->with('succes', $message);
    }

    /**
     * Heuristique d'extraction : chaque ligne du texte PDF est examinée pour
     * en tirer un matricule (jeton alphanumérique avec chiffres) et un
     * nom/prénoms (le reste du texte). Conçu pour des listes de classe au
     * format "N° Nom Prénoms Matricule" ou variantes proches ; l'utilisateur
     * corrige les erreurs éventuelles sur l'écran d'aperçu suivant.
     *
     * @return array<int, array{matricule: string, nom: string, prenoms: string}>
     */
    private function extraireEleves(string $texte): array
    {
        $lignes = preg_split('/\r\n|\r|\n/', $texte) ?: [];
        $resultat = [];

        foreach ($lignes as $ligne) {
            $ligne = trim(preg_replace('/\s+/u', ' ', $ligne) ?? '');

            if ($ligne === '') {
                continue;
            }

            // Ignore les lignes d'en-tête / pied de page évidentes.
            if (preg_match('/^(n°|no\.?|matricule|nom|classe|effectif|liste|page)\b/iu', $ligne) && ! preg_match('/\d/', $ligne)) {
                continue;
            }

            // Numéro d'ordre en début de ligne ("1.", "1)", "1 -")
            $ligne = preg_replace('/^\d{1,3}[\.\)\-\s]+/', '', $ligne) ?? $ligne;

            if ($ligne === '' || mb_strlen($ligne) < 3) {
                continue;
            }

            $matricule = '';

            // Un matricule contient au moins un chiffre, mêlé à des lettres/tirets,
            // et fait entre 3 et 20 caractères (ex: 26T-001, 2025NDA0123).
            if (preg_match('/\b(?=[A-Z0-9\-\/]{3,20}\b)(?=[A-Z0-9\-\/]*\d)[A-Z0-9\-\/]{3,20}\b/u', $ligne, $m)) {
                $matricule = $m[0];
                $ligne = trim(str_replace($matricule, ' ', $ligne));
                $ligne = trim(preg_replace('/\s+/u', ' ', $ligne) ?? '');
            }

            if ($ligne === '') {
                continue;
            }

            $mots = explode(' ', $ligne);

            // Le nom de famille est généralement en majuscules dans les listes
            // scolaires : on regroupe les mots tout-majuscules en tête comme "nom",
            // le reste comme "prénoms". À défaut, tout le texte va dans "nom".
            $nomMots = [];
            $i = 0;
            while ($i < count($mots) && $mots[$i] === mb_strtoupper($mots[$i], 'UTF-8') && preg_match('/\p{L}/u', $mots[$i])) {
                $nomMots[] = $mots[$i];
                $i++;
            }

            if (empty($nomMots)) {
                $nom = $mots[0] ?? '';
                $prenoms = implode(' ', array_slice($mots, 1));
            } else {
                $nom = implode(' ', $nomMots);
                $prenoms = implode(' ', array_slice($mots, $i));
            }

            if (trim($nom) === '') {
                continue;
            }

            $resultat[] = [
                'matricule' => $matricule,
                'nom' => trim($nom),
                'prenoms' => trim($prenoms),
            ];
        }

        return $resultat;
    }
}
