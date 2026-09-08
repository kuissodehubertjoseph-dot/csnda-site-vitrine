<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Gestion des accès à l'application, réservée au directeur général :
 * création de comptes, changement de rôle et révocation.
 */
class AccesController extends Controller
{
    public function index(): View
    {
        Gate::authorize('gerer-acces');

        $etablissementId = Student::etablissementActifId();

        return view('acces.index', [
            // Un développeur (etablissement_id null) est visible depuis
            // n'importe quel établissement ; les autres comptes ne le sont
            // que depuis leur propre établissement, pour éviter toute fuite
            // d'informations entre écoles.
            'utilisateurs' => User::where(function ($requete) use ($etablissementId) {
                $requete->whereNull('etablissement_id')->orWhere('etablissement_id', $etablissementId);
            })->orderBy('name')->get(),
            'roles' => User::ROLES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('gerer-acces');

        // Le DG peut créer un accès développeur ; en revanche il ne pourra
        // ensuite ni le modifier ni le supprimer (voir update() et destroy()).
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Un compte développeur n'est rattaché à aucun établissement
        // précis : il peut se connecter à n'importe lequel.
        $donnees['etablissement_id'] = $donnees['role'] === User::ROLE_DEVELOPPEUR
            ? null
            : Student::etablissementActifId();

        $utilisateur = User::create($donnees);

        return back()->with('succes', "Accès créé pour {$utilisateur->name} ({$utilisateur->libelleRole()}).");
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        Gate::authorize('gerer-acces');

        $donnees = $request->validate([
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        // Un compte développeur existant ne peut être touché que par un développeur.
        if ($utilisateur->estDeveloppeur() && Gate::denies('gerer-comptes-developpeur')) {
            return back()->withErrors([
                'role' => "Seul un développeur peut modifier le rôle d'un compte développeur.",
            ]);
        }

        // Empêche le dernier DG de se rétrograder : l'application deviendrait
        // impossible à administrer.
        if ($utilisateur->estDg()
            && $donnees['role'] !== User::ROLE_DG
            && User::where('role', User::ROLE_DG)->where('etablissement_id', $utilisateur->etablissement_id)->count() <= 1) {
            return back()->withErrors([
                'role' => "Impossible de retirer ce rôle : il doit rester au moins un directeur général.",
            ]);
        }

        $utilisateur->update($donnees);

        return back()->with('succes', "Rôle de {$utilisateur->name} mis à jour : {$utilisateur->libelleRole()}.");
    }

    public function destroy(Request $request, User $utilisateur): RedirectResponse
    {
        Gate::authorize('gerer-acces');

        if ($utilisateur->is($request->user())) {
            return back()->withErrors(['acces' => 'Vous ne pouvez pas supprimer votre propre accès.']);
        }

        if ($utilisateur->estDeveloppeur() && Gate::denies('gerer-comptes-developpeur')) {
            return back()->withErrors([
                'acces' => "Seul un développeur peut supprimer un compte développeur.",
            ]);
        }

        if ($utilisateur->estDg() && User::where('role', User::ROLE_DG)->where('etablissement_id', $utilisateur->etablissement_id)->count() <= 1) {
            return back()->withErrors([
                'acces' => "Impossible de supprimer le dernier directeur général.",
            ]);
        }

        $nom = $utilisateur->name;
        $utilisateur->delete();

        return back()->with('succes', "Accès de {$nom} supprimé.");
    }
}
