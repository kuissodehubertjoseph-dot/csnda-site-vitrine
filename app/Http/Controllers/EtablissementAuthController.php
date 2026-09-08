<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Connexion dédiée à un établissement précis (une page par école, accessible
 * via son slug). Distincte du login générique de routes/auth.php : elle fixe
 * en plus l'établissement actif en session, qui pilote ensuite tout
 * l'affichage (config('ecole.*') via ChargerEtablissementActif).
 */
class EtablissementAuthController extends Controller
{
    public function create(Etablissement $etablissement): View|RedirectResponse
    {
        if (! $etablissement->estActif()) {
            return redirect()->route('accueil')
                ->with('erreur', "Cet établissement n'est pas encore disponible.");
        }

        // La mise en page invitée lit config('ecole.*') pour afficher le bon
        // logo/nom : hors session (visiteur non connecté), on la renseigne
        // ici pour l'établissement demandé plutôt que d'attendre le
        // middleware ChargerEtablissementActif (qui ne s'active qu'après
        // authentification).
        config(['ecole' => $etablissement->versConfig()]);

        return view('auth.etablissement-connexion', ['etablissement' => $etablissement]);
    }

    public function store(Request $request, Etablissement $etablissement): RedirectResponse
    {
        if (! $etablissement->estActif()) {
            return redirect()->route('accueil')
                ->with('erreur', "Cet établissement n'est pas encore disponible.");
        }

        $identifiants = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($identifiants, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        $utilisateur = Auth::user();

        if (! $utilisateur->peutAccederA($etablissement)) {
            Auth::logout();

            return back()
                ->withErrors(['email' => "Ce compte n'a pas accès à cet établissement."])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('etablissement_id', $etablissement->id);

        return redirect()->route('eleves.index');
    }
}
