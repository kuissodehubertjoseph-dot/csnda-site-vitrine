<?php

namespace App\Http\Middleware;

use App\Models\Etablissement;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Recharge la config('ecole.*') à partir de l'établissement actif en session,
 * pour que tout le code existant (cartes, formulaires, navigation...) qui lit
 * config('ecole.*') affiche automatiquement les bonnes données — sans savoir
 * que l'application est multi-établissements.
 *
 * Si l'utilisateur connecté n'a plus accès à l'établissement mémorisé en
 * session (accès révoqué, établissement changé), la session est nettoyée et
 * il est renvoyé se reconnecter.
 */
class ChargerEtablissementActif
{
    public function handle(Request $request, Closure $next): Response
    {
        $utilisateur = $request->user();

        if (! $utilisateur) {
            return $next($request);
        }

        $etablissementId = $request->session()->get('etablissement_id');
        $etablissement = $etablissementId ? Etablissement::find($etablissementId) : null;

        if (! $etablissement || ! $utilisateur->peutAccederA($etablissement)) {
            $request->session()->forget('etablissement_id');
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('accueil')
                ->with('erreur', "Votre accès à cet établissement n'est plus valide. Merci de vous reconnecter.");
        }

        config(['ecole' => $etablissement->versConfig()]);
        app()->instance('etablissement.actif', $etablissement);

        return $next($request);
    }
}
