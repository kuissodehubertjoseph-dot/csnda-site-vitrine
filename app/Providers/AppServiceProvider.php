<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        /*
        |----------------------------------------------------------------------
        | Droits d'accès par rôle
        |----------------------------------------------------------------------
        | Le secrétariat gère les élèves au quotidien (ajout, modification,
        | déplacement de classe, import PDF, impression par lot, signature et
        | cachet). Ce qui est irréversible ou touche aux accès reste réservé à
        | la direction (DG et développeur).
        */
        Gate::define('supprimer-eleve', fn (User $user) => $user->estDirection());
        Gate::define('supprimer-tous-eleves', fn (User $user) => $user->estDirection());
        Gate::define('gerer-acces', fn (User $user) => $user->estDirection());

        // Ouvert à tous les rôles : le secrétariat dépose lui aussi la
        // signature du directeur et le cachet de l'établissement.
        Gate::define('gerer-parametres-carte', fn (User $user) => true);

        // Changement de classe d'un élève : opération courante, sans risque
        // de perte de données, donc accessible au secrétariat.
        Gate::define('deplacer-eleve', fn (User $user) => true);

        // Un compte développeur ne peut être créé, modifié ou supprimé que par
        // un autre développeur : le DG ne peut pas s'attribuer ce rôle.
        Gate::define('gerer-comptes-developpeur', fn (User $user) => $user->estDeveloppeur());
    }
}
