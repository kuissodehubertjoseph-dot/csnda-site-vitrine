<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Importe l'archive produite par eleves:exporter : élèves rattachés à leur
 * établissement par le slug, identifiés par (établissement, matricule), avec
 * leurs photos et signatures. Relancer la commande met à jour sans doublon.
 */
class ImporterEleves extends Command
{
    protected $signature = 'eleves:importer {fichier : Archive ZIP produite par eleves:exporter} {--dry-run : Simule sans rien écrire}';

    protected $description = 'Importe des élèves (archive ZIP) avec leurs photos et signatures';

    public function handle(): int
    {
        $fichier = $this->argument('fichier');
        $zip = new ZipArchive;

        if (! is_file($fichier) || $zip->open($fichier) !== true) {
            $this->error("Archive introuvable ou invalide : {$fichier}");

            return self::FAILURE;
        }

        $eleves = json_decode((string) $zip->getFromName('eleves.json'), true);

        if (! is_array($eleves)) {
            $this->error("L'archive ne contient pas de eleves.json valide.");

            return self::FAILURE;
        }

        $etablissements = DB::table('etablissements')->pluck('id', 'slug');
        $manquants = collect($eleves)->pluck('etablissement')->unique()->reject(fn ($s) => $etablissements->has($s));

        if ($manquants->isNotEmpty()) {
            $this->error('Établissements absents (lancez php artisan migrate) : '.$manquants->implode(', '));

            return self::FAILURE;
        }

        $crees = 0;
        $maj = 0;
        $dry = $this->option('dry-run');
        $disque = Storage::disk('public');

        DB::transaction(function () use ($eleves, $etablissements, $zip, $disque, $dry, &$crees, &$maj) {
            foreach ($eleves as $e) {
                $cle = ['etablissement_id' => $etablissements[$e['etablissement']], 'matricule' => $e['matricule']];
                $existe = DB::table('students')->where($cle)->exists();

                DB::table('students')->updateOrInsert($cle, [
                    'nom' => $e['nom'],
                    'prenoms' => $e['prenoms'],
                    'date_naissance' => $e['date_naissance'],
                    'lieu_naissance' => $e['lieu_naissance'],
                    'sexe' => $e['sexe'] ?? null,
                    'classe' => $e['classe'],
                    'telephone' => $e['telephone'],
                    'photo' => $e['photo'] ?? null,
                    'signature' => $e['signature'] ?? null,
                    'annee_scolaire' => $e['annee_scolaire'],
                    'statut' => $e['statut'] ?? 'actif',
                    'updated_at' => now(),
                ] + ($existe ? [] : ['created_at' => now()]));

                $existe ? $maj++ : $crees++;

                if ($dry) {
                    continue;
                }

                foreach (['photo', 'signature'] as $champ) {
                    $chemin = $e[$champ] ?? null;
                    $contenu = $chemin ? $zip->getFromName('fichiers/'.$chemin) : false;

                    if ($contenu !== false) {
                        $disque->put($chemin, $contenu);
                    }
                }
            }

            if ($dry) {
                DB::rollBack();
            }
        });

        $this->info(($dry ? '[simulation] ' : '')."{$crees} créés, {$maj} mis à jour.");

        return self::SUCCESS;
    }
}
