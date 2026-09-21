<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'classe' => ['nullable', 'string', 'max:50'],
            'annee_scolaire' => ['nullable', 'string', 'max:20'],
        ]);

        $texte = (new PdfParser())->parseFile($request->file('pdf')->getRealPath())->getText();

        $lignes = $this->extraireEleves($texte);

        if (empty($lignes)) {
            return back()
                ->withInput()
                ->with('erreur', "Aucune ligne exploitable n'a été trouvée dans ce PDF. Le fichier est peut-être un scan (image) plutôt qu'un PDF texte : dans ce cas, la saisie doit se faire manuellement.");
        }

        // La classe et l'année scolaire sont lues directement dans l'en-tête
        // du PDF ("Classe : 4e A", "Année scolaire : 2025-2026"). Si l'utilisateur
        // a explicitement renseigné une valeur dans le formulaire, elle prévaut.
        $classeDetectee = $this->detecterChamp($texte, 'classe');
        $anneeDetectee = $this->detecterChamp($texte, 'année scolaire');

        $classe = $request->string('classe')->trim()->toString() ?: $classeDetectee;
        $anneeScolaire = $request->string('annee_scolaire')->trim()->toString() ?: $anneeDetectee;

        return view('eleves.import.apercu', [
            'lignes' => $lignes,
            'classe' => $classe,
            'anneeScolaire' => $anneeScolaire,
            'classes' => config('ecole.classes'),
        ]);
    }

    /**
     * Enregistre les lignes validées par l'utilisateur sur l'écran d'aperçu.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'classe' => ['required', 'string', 'max:50'],
            'annee_scolaire' => ['required', 'string', 'max:20'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.inclure' => ['nullable', 'boolean'],
            'lignes.*.nom' => ['required_with:lignes.*.inclure', 'nullable', 'string', 'max:100'],
            'lignes.*.prenoms' => ['nullable', 'string', 'max:150'],
            'lignes.*.matricule' => ['nullable', 'string', 'max:50'],
            'lignes.*.sexe' => ['nullable', 'string', 'in:M,F'],
            'lignes.*.date_naissance' => ['nullable', 'date'],
            'lignes.*.lieu_naissance' => ['nullable', 'string', 'max:100'],
            'lignes.*.telephone' => ['nullable', 'string', 'max:30'],
        ]);

        $cree = 0;
        $ignoresDoublons = 0;
        $ignoresSansMatricule = 0;

        foreach ($data['lignes'] as $ligne) {
            if (empty($ligne['inclure']) || empty($ligne['nom'])) {
                continue;
            }

            // Le matricule doit obligatoirement provenir de la liste officielle :
            // on ne génère jamais de matricule à la place de l'établissement.
            $matricule = trim((string) ($ligne['matricule'] ?? ''));

            if ($matricule === '') {
                $ignoresSansMatricule++;

                continue;
            }

            if (Student::where('matricule', $matricule)->exists()) {
                $ignoresDoublons++;

                continue;
            }

            Student::create([
                'matricule' => $matricule,
                'nom' => trim($ligne['nom']),
                'prenoms' => trim((string) ($ligne['prenoms'] ?? '')),
                'sexe' => $ligne['sexe'] ?? null,
                'date_naissance' => $ligne['date_naissance'] ?? null,
                'lieu_naissance' => trim((string) ($ligne['lieu_naissance'] ?? '')) ?: null,
                'telephone' => trim((string) ($ligne['telephone'] ?? '')) ?: null,
                'classe' => $data['classe'],
                'annee_scolaire' => $data['annee_scolaire'],
                'statut' => 'actif',
            ]);

            $cree++;
        }

        $message = "{$cree} élève(s) importé(s).";
        if ($ignoresDoublons > 0) {
            $message .= " {$ignoresDoublons} ligne(s) ignorée(s) (matricule déjà existant).";
        }
        if ($ignoresSansMatricule > 0) {
            $message .= " {$ignoresSansMatricule} ligne(s) ignorée(s) (matricule manquant à compléter manuellement).";
        }
        $message .= ' Pensez à compléter les fiches incomplètes (photo, téléphone...).';

        return redirect()
            ->route('eleves.index', ['classe' => $data['classe']])
            ->with('succes', $message);
    }

    /**
     * Lit "Classe : 4e A" ou "Année scolaire : 2025-2026" dans l'en-tête du PDF.
     */
    private function detecterChamp(string $texte, string $etiquette): string
    {
        $etiquetteRegex = preg_quote($etiquette, '/');

        if (preg_match('/'.$etiquetteRegex.'\s*:\s*([^\r\n]+)/iu', $texte, $m)) {
            return trim($m[1]);
        }

        return '';
    }

    /**
     * Extraction des lignes d'élèves du texte du PDF.
     *
     * Les exports "liste de classe" officiels (format République du Bénin /
     * MESTFP) produisent des lignes du type :
     *   "1 1120823049588 ADELABOU Faïk Adéniyi M 26/08/2012 Cotonou"
     * où, selon la longueur des colonnes, le numéro d'ordre, le matricule et le
     * nom peuvent être collés sans espace (ex: "11120823049588ADELABOU"). On
     * repère d'abord la fin de ligne (sexe + date + lieu de naissance), fixe,
     * puis on isole le numéro d'ordre du matricule grâce à la convention
     * beninoise du matricule scolaire : son premier chiffre encode le sexe
     * (1 = garçon, 2 = fille).
     *
     * Un format plus simple ("Nom Prénoms Matricule", sans sexe/date/lieu)
     * reste supporté en repli, pour les listes moins détaillées.
     *
     * @return array<int, array{matricule: string, nom: string, prenoms: string, sexe: string, date_naissance: string, lieu_naissance: string}>
     */
    private function extraireEleves(string $texte): array
    {
        $lignesBrutes = preg_split('/\r\n|\r|\n/', $texte) ?: [];
        $resultat = [];

        foreach ($lignesBrutes as $ligneBrute) {
            $ligneOriginale = trim($ligneBrute);
            $ligne = trim(preg_replace('/[\t ]+/u', ' ', $ligneBrute) ?? '');

            if ($ligne === '' || mb_strlen($ligne) < 3) {
                continue;
            }

            // Ignore les lignes d'en-tête / pied de page évidentes (elles ne
            // contiennent de toute façon jamais le motif sexe+date+lieu ci-dessous).
            // Note : pas de \b après "n°", le caractère "°" n'étant pas un
            // caractère de mot, la limite ne se déclencherait jamais.
            if (preg_match('/^(année scolaire|établissement|filière|classe|n°|no\.?|matricule|nom\b|effectif|liste des|république|realized by)/iu', $ligne)) {
                continue;
            }

            // Exports en tableau (colonnes séparées par des tabulations dans le
            // flux texte du PDF, ex. UCAO) : à tenter en premier, car ce format
            // ne contient pas forcément le sexe, contrairement au format
            // Bénin/MESTFP ci-dessous.
            $ligneTableau = $this->extraireLigneTableauColonnes($ligneOriginale);

            if ($ligneTableau !== null) {
                $resultat[] = $ligneTableau;

                continue;
            }

            $ligneEnrichie = $this->extraireLigneComplete($ligne);

            if ($ligneEnrichie !== null) {
                $resultat[] = $ligneEnrichie;

                continue;
            }

            $ligneSimple = $this->extraireLigneSimple($ligne);

            if ($ligneSimple !== null) {
                $resultat[] = $ligneSimple;
            }
        }

        return $resultat;
    }

    /**
     * Tente le format complet "N°Matricule Nom Prénom(s) Sexe Date Lieu".
     *
     * @return array{matricule: string, nom: string, prenoms: string, sexe: string, date_naissance: string, lieu_naissance: string}|null
     */
    private function extraireLigneComplete(string $ligne): ?array
    {
        // Le sexe est collé à la date qui le suit (ex: "M26/08/2012"), mais
        // toujours précédé d'un espace séparant les prénoms.
        if (! preg_match('/^(?<avant>.+?)\s(?<sexe>[MF])(?<jour>\d{1,2})\/(?<mois>\d{1,2})\/(?<annee>\d{4})\s+(?<lieu>.+)$/u', $ligne, $m)) {
            return null;
        }

        $avant = trim($m['avant']);
        $sexe = $m['sexe'];
        $dateNaissance = sprintf('%04d-%02d-%02d', (int) $m['annee'], (int) $m['mois'], (int) $m['jour']);
        $lieuNaissance = $this->normaliserLieu($m['lieu']);

        // Numéro d'ordre + matricule, collés en tête ("11120823049588ADELABOU"
        // ou "10 112110161964 BOTEWA" selon la largeur de colonne du PDF).
        if (! preg_match('/^(?<chiffres>\d+)\s*(?<nomPrenoms>\D.*)$/u', $avant, $m2)) {
            return null;
        }

        $matricule = $this->deduireMatricule($m2['chiffres'], $sexe);
        $nomPrenoms = trim($m2['nomPrenoms']);

        if ($matricule === '' || $nomPrenoms === '') {
            return null;
        }

        [$nom, $prenoms] = $this->separerNomPrenoms($nomPrenoms);

        if ($nom === '') {
            return null;
        }

        return [
            'matricule' => $matricule,
            'nom' => $nom,
            'prenoms' => $prenoms,
            'sexe' => $sexe,
            'date_naissance' => $dateNaissance,
            'lieu_naissance' => $lieuNaissance,
            'telephone' => '',
        ];
    }

    /**
     * Format tableau à colonnes tabulées : "N° Matricule NOM[TAB]Prénom(s)[TAB]
     * Lieu de naissance[TAB]Date de naissance Sexe Téléphone" (ex. exports
     * UCAO). Le flux texte du PDF conserve une tabulation entre chaque
     * colonne ; c'est ce séparateur, bien plus fiable qu'une heuristique sur
     * les majuscules, qui permet de découper la ligne correctement. La
     * colonne Sexe est souvent laissée vide dans ce type d'export : elle est
     * alors à compléter manuellement sur l'écran de vérification.
     *
     * Certaines lignes perdent une tabulation quand une cellule déborde sur
     * la largeur de colonne (nom composé de deux mots, par ex.) : dans ce cas
     * on retombe sur la même heuristique "mots en MAJUSCULES = nom" que le
     * format simple, appliquée au segment restant.
     *
     * @return array{matricule: string, nom: string, prenoms: string, sexe: string, date_naissance: string, lieu_naissance: string, telephone: string}|null
     */
    private function extraireLigneTableauColonnes(string $ligneBrute): ?array
    {
        if (! str_contains($ligneBrute, "\t")) {
            return null;
        }

        $segments = array_values(array_filter(
            array_map('trim', explode("\t", $ligneBrute)),
            fn ($segment) => $segment !== ''
        ));

        if (count($segments) < 2) {
            return null;
        }

        $dernier = array_pop($segments);

        // La dernière colonne contient toujours la date de naissance, parfois
        // précédée du lieu (si sa propre tabulation a été perdue) et suivie
        // du téléphone.
        if (! preg_match('/^(?<lieu>.*?)\s*(?<jour>\d{1,2})\/(?<mois>\d{1,2})\/(?<annee>\d{4})\s*(?<telephone>[+\d][\d\s]*)?$/u', $dernier, $mDate)) {
            return null;
        }

        $dateNaissance = sprintf('%04d-%02d-%02d', (int) $mDate['annee'], (int) $mDate['mois'], (int) $mDate['jour']);
        $telephone = trim($mDate['telephone'] ?? '');
        $lieuNaissance = trim($mDate['lieu']) !== '' ? $this->normaliserLieu($mDate['lieu']) : '';

        // Première colonne restante : "N° Matricule Nom[...]" (ordre et
        // matricule séparés par un espace, contrairement au format Bénin/MESTFP).
        if (! preg_match('/^(?<ordre>\d{1,3})\s+(?<matricule>\d{4,15})\s+(?<reste>.+)$/u', $segments[0] ?? '', $mTete)) {
            return null;
        }

        $matricule = $mTete['matricule'];

        if (count($segments) >= 3) {
            // Colonnes complètes : Nom / Prénoms / Lieu chacun dans leur segment.
            $nom = trim($mTete['reste']);
            $prenoms = trim($segments[1]);

            if ($lieuNaissance === '' && isset($segments[2])) {
                $lieuNaissance = $this->normaliserLieu($segments[2]);
            }
        } elseif (count($segments) === 2) {
            $nom = trim($mTete['reste']);
            $prenoms = trim($segments[1]);
        } else {
            // Nom et prénoms compressés dans le même segment : on retombe sur
            // l'heuristique "mots en MAJUSCULES en tête = nom".
            [$nom, $prenoms] = $this->separerNomPrenoms(trim($mTete['reste']));
        }

        if ($matricule === '' || $nom === '') {
            return null;
        }

        return [
            'matricule' => $matricule,
            'nom' => $nom,
            'prenoms' => $prenoms,
            'sexe' => '',
            'date_naissance' => $dateNaissance,
            'lieu_naissance' => $lieuNaissance,
            'telephone' => $telephone,
        ];
    }

    /**
     * Repli pour un format plus simple, sans sexe/date/lieu détectés :
     * "N° Nom Prénoms Matricule" ou variantes proches.
     *
     * @return array{matricule: string, nom: string, prenoms: string, sexe: string, date_naissance: string, lieu_naissance: string}|null
     */
    private function extraireLigneSimple(string $ligne): ?array
    {
        // Numéro d'ordre en début de ligne ("1.", "1)", "1 -", "1 ")
        $ligne = preg_replace('/^\d{1,3}[\.\)\-\s]+/', '', $ligne) ?? $ligne;

        if ($ligne === '' || mb_strlen($ligne) < 3) {
            return null;
        }

        $matricule = '';

        // Un matricule contient au moins un chiffre, mêlé à des lettres/tirets,
        // et fait entre 3 et 20 caractères (ex: 26T-001, 1130823020629).
        if (preg_match('/\b(?=[A-Z0-9\-\/]{3,20}\b)(?=[A-Z0-9\-\/]*\d)[A-Z0-9\-\/]{3,20}\b/u', $ligne, $m)) {
            $matricule = $m[0];
            $ligne = trim(str_replace($matricule, ' ', $ligne));
            $ligne = trim(preg_replace('/\s+/u', ' ', $ligne) ?? '');
        }

        if ($ligne === '') {
            return null;
        }

        [$nom, $prenoms] = $this->separerNomPrenoms($ligne);

        if ($nom === '') {
            return null;
        }

        return [
            'matricule' => $matricule,
            'nom' => $nom,
            'prenoms' => $prenoms,
            'sexe' => '',
            'date_naissance' => '',
            'lieu_naissance' => '',
            'telephone' => '',
        ];
    }

    /**
     * Le nom de famille est généralement en majuscules dans les listes
     * scolaires : on regroupe les mots tout-majuscules en tête comme "nom",
     * le reste comme "prénoms". À défaut, tout le texte va dans "nom".
     *
     * @return array{0: string, 1: string} [nom, prénoms]
     */
    private function separerNomPrenoms(string $texte): array
    {
        $mots = explode(' ', $texte);

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

        return [trim($nom), trim($prenoms)];
    }

    /**
     * Isole le matricule dans un bloc de chiffres "numéro d'ordre + matricule"
     * collés (ex: "16212110161953" = ordre "16" + matricule "212110161953").
     *
     * Convention du matricule scolaire béninois : le premier chiffre encode le
     * sexe (1 = garçon, 2 = fille), et sa longueur est de 12 ou 13 chiffres.
     * On essaie chaque découpage plausible et on retient celui dont le premier
     * chiffre correspond au sexe déjà lu sur la ligne.
     */
    private function deduireMatricule(string $chiffres, string $sexe): string
    {
        $attendu = $sexe === 'F' ? '2' : '1';
        $longueurTotale = strlen($chiffres);

        $candidats = [];
        foreach ([13, 12] as $longueurMatricule) {
            $longueurOrdre = $longueurTotale - $longueurMatricule;

            if ($longueurOrdre < 1 || $longueurOrdre > 3) {
                continue;
            }

            $candidats[] = substr($chiffres, $longueurOrdre);
        }

        foreach ($candidats as $candidat) {
            if ($candidat[0] === $attendu) {
                return $candidat;
            }
        }

        // Aucun candidat ne correspond au sexe attendu : on retient le premier
        // qui ne commence pas par un zéro (un matricule ne commence jamais par 0).
        foreach ($candidats as $candidat) {
            if ($candidat[0] !== '0') {
                return $candidat;
            }
        }

        // Dernier repli : tout le bloc de chiffres, tel quel.
        return $chiffres;
    }

    /**
     * Met en forme le lieu de naissance ("COTONOU" / "cotonou" -> "Cotonou"),
     * sans toucher aux noms déjà correctement casés ("Abomey Calavi").
     */
    private function normaliserLieu(string $lieu): string
    {
        $lieu = trim($lieu);

        if ($lieu === mb_strtoupper($lieu, 'UTF-8') || $lieu === mb_strtolower($lieu, 'UTF-8')) {
            return mb_convert_case($lieu, MB_CASE_TITLE, 'UTF-8');
        }

        return $lieu;
    }
}
