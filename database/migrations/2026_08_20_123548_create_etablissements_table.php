<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un établissement = un espace totalement isolé (élèves, comptes, cartes).
     * Les colonnes reprennent exactement les clés de config/ecole.php : le
     * middleware ChargerEtablissementActif recharge ce tableau de config à
     * partir de la ligne active en base, si bien qu'aucun code existant qui
     * lit config('ecole.*') n'a besoin d'être modifié.
     */
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->enum('statut', ['actif', 'bientot'])->default('bientot');

            $table->string('nom');
            $table->string('sigle');
            $table->string('ville')->nullable();
            $table->string('slogan')->nullable();
            $table->string('telephone_secretariat')->nullable();
            $table->string('logo')->nullable();

            $table->string('tutelle_ligne1')->nullable();
            $table->string('tutelle_ligne2')->nullable();
            $table->string('nom_ligne1')->nullable();
            $table->string('nom_ligne2')->nullable();

            $table->string('adresse')->nullable();
            $table->string('directeur')->nullable();

            $table->string('signature')->nullable();
            $table->string('cachet')->nullable();

            $table->json('classes')->nullable();
            $table->string('annee_scolaire_courante')->nullable();

            $table->timestamps();
        });

        // Le collège CSS est le tout premier établissement de l'application :
        // on le crée ici à partir des valeurs déjà présentes dans
        // config/ecole.php, pour que les élèves et comptes existants (migrés
        // par les migrations suivantes) aient un établissement à rejoindre.
        DB::table('etablissements')->insert([
            'slug' => 'css',
            'statut' => 'actif',
            'nom' => config('ecole.nom'),
            'sigle' => config('ecole.sigle'),
            'ville' => config('ecole.ville'),
            'slogan' => config('ecole.slogan'),
            'telephone_secretariat' => config('ecole.telephone_secretariat'),
            'logo' => config('ecole.logo'),
            'tutelle_ligne1' => config('ecole.tutelle_ligne1'),
            'tutelle_ligne2' => config('ecole.tutelle_ligne2'),
            'nom_ligne1' => config('ecole.nom_ligne1'),
            'nom_ligne2' => config('ecole.nom_ligne2'),
            'adresse' => config('ecole.adresse'),
            'directeur' => config('ecole.directeur'),
            'signature' => config('ecole.signature'),
            'cachet' => config('ecole.cachet'),
            'classes' => json_encode(config('ecole.classes')),
            'annee_scolaire_courante' => config('ecole.annee_scolaire_courante'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};
