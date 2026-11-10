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

        $classe = $request->string('classe')->trim()->toString()
            ?: $this->classeOfficielle($classeDetectee)
            ?: $this->classeDepuisFiliere($texte);
        $anneeScolaire = $request->string('annee_scolaire')->trim()->toString() ?: $anneeDetectee;

        // Conservé en session pour que l'aperçu survive à un rechargement ou
        // à un retour après une erreur de validation à l'enregistrement.
        $request->session()->put('import_apercu', [
            'lignes' => $lignes,
            'classe' => $classe,
            'anneeScolaire' => $anneeScolaire,
        ]);

        return redirect()->route('eleves.import.apercu.afficher');
    }

    public function afficherApercu(Request $request): View|RedirectResponse
    {
        $apercu = $request->session()->get('import_apercu');

        if (! $apercu) {
            return redirect()->route('eleves.import.form');
        }

        return view('eleves.import.apercu', [
            'lignes' => $apercu['lignes'],
            'classe' => $apercu['classe'],
            'anneeScolaire' => $apercu['anneeScolaire'],
            'classes' => config('ecole.classes'),
        ]);
    }

    /**
     * Enregistre les lignes validées par l'utilisateur sur l'écran d'aperçu.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'classe' => ['required', 'string', \Illuminate\Validation\Rule::in(config('ecole.classes') ?? [])],
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
        ], [
            'classe.required' => 'Merci de renseigner la classe.',
            'classe.in' => 'La classe « :input » ne fait pas partie de la liste des classes de l\'établissement : choisissez-la dans la liste proposée.',
            'lignes.*.*.max' => 'Ligne :position : un champ est trop long (:max caractères maximum).',
            'lignes.*.*.date' => 'Ligne :position : date de naissance invalide.',
            'lignes.*.*.required_with' => 'Ligne :position : le nom est obligatoire.',
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

        $request->session()->forget('import_apercu');

        return redirect()
            ->route('eleves.index', ['classe' => $data['classe']])
            ->with('succes', $message);
    }

    /**
     * Lit "Classe : 4e A" ou "Année scolaire : 2025-2026" dans l'en-tête du
     * PDF. Le libellé peut être suivi de mots supplémentaires avant le
     * ":" (ex. "Liste des élèves de la classe de : 1ère-B"), d'où le
     * "(?:\s+\S+){0,3}" toléré entre l'étiquette et le ":".
     */
    private function detecterChamp(string $texte, string $etiquette): string
    {
        $etiquetteRegex = preg_quote($etiquette, '/');

        if (preg_match('/'.$etiquetteRegex.'(?:\s+\S+){0,3}?\s*:\s*([^\r\n]+)/iu', $texte, $m)) {
            return trim($m[1]);
        }

        return '';
    }

    /**
     * Ramène la classe lue dans le PDF ("6 ème-E2", "Terminale-B", "2nd G-2")
     * à son libellé officiel dans la liste de l'établissement ("6ème-E2",
     * "TleB", "2nde-G2"). Sans correspondance sûre, renvoie le texte lu.
     */
    private function classeOfficielle(string $classeLue): string
    {
        if ($classeLue === '') {
            return '';
        }

        $cle = fn (string $c) => preg_replace(
            ['/^terminale|^tle/', '/^(\d)(eme|ere|er|nde|nd|e)/'],
            ['t', '$1'],
            preg_replace('/[^a-z0-9]/', '', strtolower(\Illuminate\Support\Str::ascii($c)))
        );

        $cleLue = $cle($classeLue);
        $candidats = [];

        foreach (config('ecole.classes') ?? [] as $officielle) {
            $cleOfficielle = $cle($officielle);

            if ($cleOfficielle === $cleLue) {
                return $officielle;
            }

            if (str_starts_with($cleLue, $cleOfficielle) || str_starts_with($cleOfficielle, $cleLue)) {
                $candidats[] = $officielle;
            }
        }

        return count($candidats) === 1 ? $candidats[0] : $classeLue;
    }

    /**
     * Filières universitaires (UCAO, EGEI, FSAE, FDE) : intitulé complet tel
     * qu'il apparaît dans l'en-tête des listes officielles => abréviation
     * utilisée dans les classes de l'établissement ("Licence 1 (TL)").
     */
    private const ABREVIATIONS_FILIERES = [
        'action commerciale et force de vente' => 'ACFV',
        'assurance' => 'ASSU',
        'audit et controle de gestion' => 'ACG',
        "banque et finance d'entreprise" => 'BFE',
        'commerce' => 'COM',
        'communication et action publicitaire' => 'CAP',
        'informatique de gestion' => 'IG',
        'management des ressources humaines' => 'MRH',
        'transport et logistique' => 'TL',
        'commerce international' => 'CI',
        'marketing et communication' => 'MC',
        'audit et securite des systemes et reseaux informatiques' => 'ASSRI',
        'electronique' => 'ELN',
        'electrotechnique' => 'ELT',
        'genie telecoms et tic' => 'GTTIC',
        'informatique industrielle et maintenance' => 'IIM',
        'maintenance biomedicale et equipements hospitaliers' => 'MBEH',
        'systeme informatique et logiciel' => 'SIL',
        'telecommunications et reseaux informatiques' => 'TRI',
        'agronomie' => 'AGRO',
        'production et gestion des ressources animales' => 'PGRA',
        "gestion de l'environnement et amenagement du territoire" => 'GEAT',
        'qualite hygiene securite environnement' => 'QHSE',
        'tourisme' => 'TOUR',
        'droit' => 'DRT',
        'economie' => 'ECO',
        'econometrie et statistique appliquee' => 'ESA',
        'droit prive fondamental' => 'DPRF',
        'droit public fondamental' => 'DPUF',
    ];

    /**
     * Listes universitaires sans ligne "Classe :" (ex. UCAO) : l'en-tête
     * donne la filière ("Filière : TRANSPORT ET LOGISTIQUE") et le niveau
     * sur une ligne à part ("LICENCE 1"), qu'on recompose en "Licence 1 (TL)"
     * lorsque cette classe existe dans l'établissement.
     */
    private function classeDepuisFiliere(string $texte): string
    {
        $filiere = $this->detecterChamp($texte, 'filière');

        if ($filiere === '' || ! preg_match('/^\s*(licence|master)\s*(\d)\s*$/imu', $texte, $mNiveau)) {
            return '';
        }

        $cle = mb_strtolower(\Illuminate\Support\Str::ascii($filiere), 'UTF-8');
        $cle = trim(preg_replace('/\s+/u', ' ', $cle) ?? '');
        $abreviation = self::ABREVIATIONS_FILIERES[$cle] ?? null;

        if ($abreviation === null) {
            return '';
        }

        $cycle = ucfirst(strtolower($mNiveau[1]));
        $classesConnues = config('ecole.classes') ?? [];

        // Master : "Master 1 (X)" ou, à l'EGEI, "Master (X)" sans numéro.
        foreach ([$cycle.' '.$mNiveau[2].' ('.$abreviation.')', $cycle.' ('.$abreviation.')'] as $classe) {
            if (in_array($classe, $classesConnues, true)) {
                return $classe;
            }
        }

        return '';
    }

    /**
     * Ligne d'en-tête, de pied de page ou de titre à ignorer. Aucun format
     * d'élève supporté ne produit de ligne sans chiffre (matricule/date).
     */
    private function estLigneDeBruit(string $ligne): bool
    {
        if (! preg_match('/\d/u', $ligne)) {
            return true;
        }

        return $this->estEnTeteOuPiedDePage($ligne);
    }

    private function estEnTeteOuPiedDePage(string $ligne): bool
    {
        return preg_match(
            '/^(année scolaire|établissement|filière|classe|n°|no[\.\s°]|numéro|matricule|nom\b|naissance|lieu de naissance|date de|effectif|liste des|république|realized by|page\s*\d+\s*\/\s*\d+|édité (le|par)|(licence|master)\s*\d\s*$)/iu',
            $ligne
        ) === 1 || str_contains($ligne, '©');
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
        $lignesBrutes = $this->fusionnerLignesRepliees(preg_split('/\r\n|\r|\n/', $texte) ?: []);
        $resultat = [];

        foreach ($lignesBrutes as $ligneBrute) {
            $ligneOriginale = trim($ligneBrute);
            $ligne = trim(preg_replace('/[\t ]+/u', ' ', $ligneBrute) ?? '');

            if ($ligne === '' || mb_strlen($ligne) < 3) {
                continue;
            }

            if ($this->estLigneDeBruit($ligne)) {
                continue;
            }

            // Export UCAO "Matricule Nom Date Lieu Pays Nationalité Téléphone",
            // reconnaissable à sa date au format ISO (2007-05-20).
            $ligneMatriculeDateIso = $this->extraireLigneMatriculeDateIso($ligneOriginale);

            if ($ligneMatriculeDateIso !== null) {
                $resultat[] = $ligneMatriculeDateIso;

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

            $ligneCollee = $this->extraireLigneNomOrdreSexeStatutMatriculeDate($ligneOriginale);

            if ($ligneCollee !== null) {
                $resultat[] = $ligneCollee;

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
     * Recolle les lignes qu'un export en tableau (ex. UCAO) a coupées sur
     * plusieurs lignes de texte quand une cellule (le plus souvent les
     * prénoms) déborde de la hauteur prévue pour la ligne — parfois jusqu'à
     * couper un mot en plein milieu ("Marie-" / "Madeleine" sur deux lignes).
     *
     * Une ligne démarre un nouvel élève quand elle commence par le numéro
     * d'ordre suivi du matricule ("5 2500222001 ...") ; toute ligne suivante
     * qui n'en a pas la forme, et qui n'est pas elle-même une ligne d'en-tête
     * / pied de page, est recollée à la précédente.
     *
     * @param  array<int, string>  $lignes
     * @return array<int, string>
     */
    private function fusionnerLignesRepliees(array $lignes): array
    {
        $resultat = [];
        $courante = null;

        foreach ($lignes as $ligne) {
            $ligneBrute = rtrim($ligne, "\r\n");
            $ligneNettoyee = trim(preg_replace('/[\t ]+/u', ' ', $ligneBrute) ?? '');

            if ($ligneNettoyee === '' || mb_strlen($ligneNettoyee) < 2) {
                continue;
            }

            // Export UCAO dont les prénoms ou le lieu de naissance débordent
            // ("GBELODE" / "FRANCINE" / "2008-05-01GODOMEY...", ou "...
            // République Démocratique" / "du Congo" / "Congo[TAB]+229 ...") :
            // la ligne sans chiffre du milieu passerait pour du bruit et
            // couperait l'élève en deux. Tant que la ligne en cours n'a pas
            // atteint son téléphone (dernière colonne), on l'y recolle.
            if ($courante !== null
                && ! preg_match('/\d/u', $ligneNettoyee)
                && ! $this->estEnTeteOuPiedDePage($ligneNettoyee)
                && preg_match('/^\d{6,15}\s+\p{L}/u', trim($courante))
                && ! preg_match('/\+?\d[\d\s]{6,}$/u', trim($courante))) {
                $courante = rtrim($courante).' '.ltrim($ligneBrute);

                continue;
            }

            if ($this->estLigneDeBruit($ligneNettoyee)) {
                if ($courante !== null) {
                    $resultat[] = $courante;
                    $courante = null;
                }

                continue;
            }

            $debuteNouvelElement = preg_match('/^\d{1,3}\s+\d{4,15}\b/u', $ligneNettoyee) === 1
                || preg_match('/^\d{8,15}\s+\p{L}/u', $ligneNettoyee) === 1
                || $this->extraireLigneNomOrdreSexeStatutMatriculeDate($ligneNettoyee) !== null;

            if ($debuteNouvelElement || $courante === null) {
                if ($courante !== null) {
                    $resultat[] = $courante;
                }

                $courante = $ligneBrute;

                continue;
            }

            // Continuation de la ligne précédente : un mot coupé en fin de
            // ligne ("Marie-") se rattache directement, sinon on rejoint
            // avec un espace.
            $courante = str_ends_with(rtrim($courante), '-')
                ? rtrim($courante).ltrim($ligneBrute)
                : rtrim($courante).' '.ltrim($ligneBrute);
        }

        if ($courante !== null) {
            $resultat[] = $courante;
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
     * Format tableau : "N° Matricule NOM[TAB]Prénom(s)[TAB]Lieu de
     * naissance[TAB]Date de naissance Sexe Téléphone" (ex. exports UCAO).
     * Le flux texte du PDF conserve en principe une tabulation entre chaque
     * colonne, mais elle disparaît par endroits quand une cellule déborde de
     * la largeur prévue (nom en plusieurs mots, prénoms longs...) : selon le
     * cas, c'est la frontière nom/prénoms qui saute, ou celle
     * prénoms/lieu, ou même toutes les deux à la fois. On repère donc
     * d'abord la date n'importe où dans la ligne (ancre fiable, jamais
     * ambiguë), puis on ne s'appuie sur les tabulations restantes que
     * lorsqu'elles existent, avec un repli heuristique sinon.
     *
     * Contrairement au format Bénin/MESTFP, le sexe n'est jamais collé à la
     * date ici : la colonne Sexe est le plus souvent laissée vide dans ce
     * type d'export, à compléter manuellement sur l'écran de vérification.
     * On exclut donc explicitement les lignes qui portent la signature du
     * format MESTFP (sexe collé, ex. "M26/08/2012"), pour leur laisser
     * suivre extraireLigneComplete() sans risque de confusion.
     *
     * @return array{matricule: string, nom: string, prenoms: string, sexe: string, date_naissance: string, lieu_naissance: string, telephone: string}|null
     */
    private function extraireLigneTableauColonnes(string $ligneBrute): ?array
    {
        if (preg_match('/\s[MF]\d{1,2}\/\d{1,2}\/\d{4}/u', $ligneBrute)) {
            return null;
        }

        if (! preg_match('/^(?<avant>.+?)\s*(?<jour>\d{1,2})\/(?<mois>\d{1,2})\/(?<annee>\d{4})\s*(?<telephone>[+\d][\d\s]*)?$/u', trim($ligneBrute), $mDate)) {
            return null;
        }

        $dateNaissance = sprintf('%04d-%02d-%02d', (int) $mDate['annee'], (int) $mDate['mois'], (int) $mDate['jour']);
        $telephone = trim($mDate['telephone'] ?? '');

        // Numéro d'ordre + matricule, séparés par un espace (contrairement au
        // format Bénin/MESTFP où ils sont collés).
        if (! preg_match('/^(?<ordre>\d{1,3})\s+(?<matricule>\d{4,15})\s+(?<reste>.+)$/u', trim($mDate['avant']), $mTete)) {
            return null;
        }

        $matricule = $mTete['matricule'];

        $morceaux = array_values(array_filter(
            array_map('trim', explode("\t", $mTete['reste'])),
            fn ($morceau) => $morceau !== ''
        ));

        if (empty($morceaux)) {
            return null;
        }

        [$nom, $blocRestant] = $this->separerNomPrenoms($morceaux[0]);

        if ($nom === '') {
            return null;
        }

        if ($blocRestant !== '') {
            if (count($morceaux) > 1) {
                // Prénoms trouvés en fin de 1er segment, lieu déjà isolé par
                // sa propre tabulation dans le(s) segment(s) suivant(s).
                $prenoms = $blocRestant;
                $lieuNaissance = implode(' ', array_slice($morceaux, 1));
            } else {
                // Aucune tabulation restante : prénoms et lieu sont glués.
                [$prenoms, $lieuNaissance] = $this->separerPrenomsLieu($blocRestant);
            }
        } elseif (count($morceaux) >= 3) {
            // Nom / Prénoms / Lieu, chacun dans son propre segment.
            $prenoms = $morceaux[1];
            $lieuNaissance = implode(' ', array_slice($morceaux, 2));
        } elseif (count($morceaux) === 2) {
            // Nom isolé, mais prénoms et lieu glués dans le 2e segment.
            [$prenoms, $lieuNaissance] = $this->separerPrenomsLieu($morceaux[1]);
        } else {
            $prenoms = '';
            $lieuNaissance = '';
        }

        if ($matricule === '') {
            return null;
        }

        return [
            'matricule' => $matricule,
            'nom' => $nom,
            'prenoms' => trim($prenoms),
            'sexe' => '',
            'date_naissance' => $dateNaissance,
            'lieu_naissance' => $lieuNaissance !== '' ? $this->normaliserLieu($lieuNaissance) : '',
            'telephone' => $telephone,
        ];
    }

    /**
     * Pays les plus fréquents dans les listes, pour les détacher du lieu de
     * naissance quand la tabulation qui les sépare a disparu.
     */
    private const PAYS = [
        'Bénin', 'Togo', 'Nigeria', 'Niger', 'Burkina Faso', "Côte d'Ivoire",
        'Ghana', 'Gabon', 'Cameroun', 'Sénégal', 'Mali', 'Tchad', 'Guinée',
        'République Démocratique du Congo', 'Congo', 'France',
    ];

    /**
     * Format UCAO (liste par filière) : "Matricule NOM Prénom(s)[TAB]
     * AAAA-MM-JJLieu[TAB]Pays[TAB]Nationalité[TAB]Téléphone", sans numéro
     * d'ordre. La date au format ISO, toujours présente et collée au lieu,
     * sert d'ancre. Comme dans les autres exports, les tabulations entre
     * lieu, pays et nationalité disparaissent par endroits ("AHOLOUYEME
     * Bénin[TAB]Bénin") : le lieu est la première colonne après la date,
     * débarrassée des noms de pays qui lui restent collés en fin.
     *
     * @return array{matricule: string, nom: string, prenoms: string, sexe: string, date_naissance: string, lieu_naissance: string, telephone: string}|null
     */
    private function extraireLigneMatriculeDateIso(string $ligneBrute): ?array
    {
        if (! preg_match(
            '/^(?<matricule>\d{6,15})\s+(?<nomPrenoms>\D+?)\s*(?<annee>\d{4})-(?<mois>\d{2})-(?<jour>\d{2})(?<reste>.*)$/u',
            trim($ligneBrute),
            $m
        )) {
            return null;
        }

        $reste = $m['reste'];
        $telephone = '';

        if (preg_match('/\s*(?<telephone>\+?\d[\d\s]{6,})$/u', $reste, $mTel)) {
            $telephone = trim($mTel['telephone']);
            $reste = substr($reste, 0, -strlen($mTel[0]));
        }

        $colonnes = array_values(array_filter(
            array_map('trim', explode("\t", $reste)),
            fn ($colonne) => $colonne !== ''
        ));

        $lieu = $colonnes[0] ?? '';
        $pays = array_merge(array_slice($colonnes, 1), self::PAYS);

        do {
            $avant = $lieu;

            foreach ($pays as $unPays) {
                if (mb_strlen($lieu) > mb_strlen($unPays)
                    // Pays séparé par un espace, ou collé ("AdjibameyBénin").
                    && (preg_match('/\s'.preg_quote($unPays, '/').'$/iu', $lieu)
                        || preg_match('/(?<=\p{Ll})'.preg_quote($unPays, '/').'$/u', $lieu))) {
                    $lieu = trim(mb_substr($lieu, 0, mb_strlen($lieu) - mb_strlen($unPays)));
                }
            }
        } while ($lieu !== $avant);

        $nomPrenoms = trim(preg_replace('/\s+/u', ' ', $m['nomPrenoms']) ?? '');
        [$nom, $prenoms] = $this->separerNomPrenoms($nomPrenoms);

        // Nom et prénoms entièrement en majuscules : premier mot comme nom,
        // à vérifier à l'aperçu.
        if ($prenoms === '' && str_contains($nom, ' ')) {
            [$nom, $prenoms] = explode(' ', $nom, 2);
            $prenoms = mb_convert_case(mb_strtolower($prenoms, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        }

        if ($nom === '') {
            return null;
        }

        return [
            'matricule' => $m['matricule'],
            'nom' => $nom,
            'prenoms' => $prenoms,
            'sexe' => '',
            'date_naissance' => sprintf('%04d-%02d-%02d', (int) $m['annee'], (int) $m['mois'], (int) $m['jour']),
            'lieu_naissance' => $lieu !== '' ? $this->normaliserLieu($lieu) : '',
            'telephone' => $telephone,
        ];
    }

    /**
     * Sépare un bloc "Prénom(s) Lieu" glué (tabulation perdue à la mise en
     * page) en isolant le dernier mot comme lieu de naissance : heuristique
     * raisonnable pour les listes béninoises (le lieu suit toujours les
     * prénoms), à vérifier sur l'écran de contrôle si le prénom compte
     * lui-même plusieurs mots.
     *
     * @return array{0: string, 1: string} [prénoms, lieu]
     */
    private function separerPrenomsLieu(string $bloc): array
    {
        $mots = preg_split('/\s+/u', trim($bloc)) ?: [];

        if (count($mots) < 2) {
            return [trim($bloc), ''];
        }

        $lieu = array_pop($mots);

        return [implode(' ', $mots), $lieu];
    }

    /**
     * Tente le format "NOM Prénoms" + N° + Sexe + Statut + Matricule + Date +
     * Lieu, entièrement collés sans espace ni tabulation (ex. export "Collège
     * Catholique Saint Jean-Baptiste") : contrairement aux autres formats, le
     * flux texte du PDF ne conserve ici aucun séparateur entre colonnes — une
     * ligne ressemble à "ADANVEHINTO Augustin1MP20602828/05/2010Cotonou". Le
     * sexe (M/F) suivi de la lettre de statut (P/R/N...) sert d'ancre fixe
     * entre le numéro d'ordre et le matricule ; les barres obliques de la
     * date bornent ensuite, via un matricule capturé de façon paresseuse, la
     * fin du matricule (longueur variable selon l'établissement) du jour de
     * naissance qui le suit immédiatement.
     *
     * @return array{matricule: string, nom: string, prenoms: string, sexe: string, date_naissance: string, lieu_naissance: string, telephone: string}|null
     */
    private function estParticule(string $mot): bool
    {
        return preg_match("/^(de|da|do|dos|du|des|del|della|le|la|van|von|d')$/iu", $mot) === 1;
    }

    private function extraireLigneNomOrdreSexeStatutMatriculeDate(string $ligne): ?array
    {
        if (! preg_match(
            '/^(?<nomPrenoms>\D+?)(?<ordre>\d{1,3})(?<sexe>[MF])(?<statut>[A-Z])(?<matricule>\d{2,15}?)(?<jour>\d{1,2})\/(?<mois>\d{1,2})\/(?<annee>\d{4})(?<lieu>.*)$/u',
            $ligne,
            $m
        )) {
            return null;
        }

        [$nom, $prenoms] = $this->separerNomPrenoms(trim($m['nomPrenoms']));

        // Nom et prénoms saisis entièrement en majuscules ("DEGBEOVI JOSE
        // ANTOINE") : on prend le premier mot comme nom, à vérifier à l'aperçu.
        if ($prenoms === '' && str_contains($nom, ' ')) {
            [$nom, $prenoms] = explode(' ', $nom, 2);
            $prenoms = mb_convert_case(mb_strtolower($prenoms, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        }

        if ($nom === '' || $m['matricule'] === '') {
            return null;
        }

        return [
            'matricule' => $m['matricule'],
            'nom' => $nom,
            'prenoms' => $prenoms,
            'sexe' => $m['sexe'],
            'date_naissance' => sprintf('%04d-%02d-%02d', (int) $m['annee'], (int) $m['mois'], (int) $m['jour']),
            'lieu_naissance' => $this->normaliserLieu(trim($m['lieu'])),
            'telephone' => '',
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

        // Particule en tête ("De SOUZA", "Do REGO") : fait partie du nom.
        if (count($mots) > 1 && $this->estParticule($mots[0]) && $mots[1] === mb_strtoupper($mots[1], 'UTF-8')) {
            $nomMots[] = $mots[0];
            $i = 1;
        }
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
