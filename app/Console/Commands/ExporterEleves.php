<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/** Exporte les élèves d'établissements donnés vers un fichier JSON (voir eleves:importer). */
class ExporterEleves extends Command
{
    protected $signature = 'eleves:exporter {slugs* : Slugs des établissements} {--fichier=storage/app/export-eleves.json}';

    protected $description = 'Exporte les élèves des établissements indiqués en JSON';

    public function handle(): int
    {
        $eleves = DB::table('students')
            ->join('etablissements', 'etablissements.id', '=', 'students.etablissement_id')
            ->whereIn('etablissements.slug', $this->argument('slugs'))
            ->orderBy('etablissements.slug')->orderBy('students.classe')->orderBy('students.nom')
            ->get(['etablissements.slug as etablissement', 'students.matricule', 'students.nom', 'students.prenoms', 'students.date_naissance',
                'students.lieu_naissance', 'students.classe', 'students.telephone', 'students.annee_scolaire', 'students.statut as statut'])
            ->all();

        $fichier = $this->option('fichier');
        file_put_contents($fichier, json_encode($eleves, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info(count($eleves)." élèves exportés vers {$fichier}.");

        return self::SUCCESS;
    }
}
