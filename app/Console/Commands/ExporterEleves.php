<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Exporte les élèves (tous, ou ceux d'établissements donnés) avec leurs
 * photos et signatures dans une archive ZIP (voir eleves:importer).
 */
class ExporterEleves extends Command
{
    protected $signature = 'eleves:exporter {slugs?* : Slugs des établissements (tous si omis)} {--fichier=storage/app/export-eleves.zip}';

    protected $description = 'Exporte les élèves, leurs photos et signatures dans une archive ZIP';

    public function handle(): int
    {
        $requete = DB::table('students')
            ->join('etablissements', 'etablissements.id', '=', 'students.etablissement_id')
            ->orderBy('etablissements.slug')->orderBy('students.classe')->orderBy('students.nom');

        if ($this->argument('slugs')) {
            $requete->whereIn('etablissements.slug', $this->argument('slugs'));
        }

        $eleves = $requete->get([
            'etablissements.slug as etablissement', 'students.matricule', 'students.nom', 'students.prenoms',
            'students.date_naissance', 'students.lieu_naissance', 'students.sexe', 'students.classe',
            'students.telephone', 'students.photo', 'students.signature', 'students.annee_scolaire',
            'students.statut as statut',
        ]);

        $zip = new ZipArchive;
        $fichier = $this->option('fichier');

        if ($zip->open($fichier, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error("Impossible de créer {$fichier}.");

            return self::FAILURE;
        }

        $disque = Storage::disk('public');
        $fichiers = 0;
        $absents = 0;

        foreach ($eleves as $e) {
            foreach (['photo', 'signature'] as $champ) {
                if (! $e->$champ) {
                    continue;
                }

                if ($disque->exists($e->$champ)) {
                    $zip->addFile($disque->path($e->$champ), 'fichiers/'.$e->$champ);
                    $fichiers++;
                } else {
                    $absents++;
                }
            }
        }

        $zip->addFromString('eleves.json', json_encode($eleves, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $zip->close();

        $this->info(count($eleves)." élèves et {$fichiers} fichiers (photos/signatures) exportés vers {$fichier}.");

        if ($absents) {
            $this->warn("{$absents} fichiers référencés sont introuvables et n'ont pas été inclus.");
        }

        return self::SUCCESS;
    }
}
