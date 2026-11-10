<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Importe des élèves depuis un fichier JSON produit par eleves:exporter.
 * Chaque élève est rattaché à son établissement par le slug et identifié par
 * (établissement, matricule) : relancer la commande met à jour sans doublon.
 */
class ImporterEleves extends Command
{
    protected $signature = 'eleves:importer {fichier : Chemin du fichier JSON} {--dry-run : Simule sans rien écrire}';

    protected $description = 'Importe des élèves (export JSON) dans leurs établissements';

    public function handle(): int
    {
        $fichier = $this->argument('fichier');

        if (! is_file($fichier)) {
            $this->error("Fichier introuvable : {$fichier}");

            return self::FAILURE;
        }

        $eleves = json_decode(file_get_contents($fichier), true);

        if (! is_array($eleves)) {
            $this->error('Le fichier JSON est invalide.');

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

        DB::transaction(function () use ($eleves, $etablissements, &$crees, &$maj) {
            foreach ($eleves as $e) {
                $cle = ['etablissement_id' => $etablissements[$e['etablissement']], 'matricule' => $e['matricule']];
                $existe = DB::table('students')->where($cle)->exists();

                DB::table('students')->updateOrInsert($cle, [
                    'nom' => $e['nom'],
                    'prenoms' => $e['prenoms'],
                    'date_naissance' => $e['date_naissance'],
                    'lieu_naissance' => $e['lieu_naissance'],
                    'classe' => $e['classe'],
                    'telephone' => $e['telephone'],
                    'annee_scolaire' => $e['annee_scolaire'],
                    'statut' => $e['statut'] ?? 'actif',
                    'updated_at' => now(),
                ] + ($existe ? [] : ['created_at' => now()]));

                $existe ? $maj++ : $crees++;
            }

            if ($this->option('dry-run')) {
                DB::rollBack();
            }
        });

        $this->info(($this->option('dry-run') ? '[simulation] ' : '')."{$crees} créés, {$maj} mis à jour.");

        return self::SUCCESS;
    }
}
