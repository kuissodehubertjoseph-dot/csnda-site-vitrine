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
            'utilisateurs' => User::where('etablissement_id', $etablissementId)
                ->where('role', '!=', User::ROLE_DEVELOPPEUR)
                ->orderBy('name')
                ->get(),
            'roles' => array_diff_key(User::ROLES, [User::ROLE_DEVELOPPEUR => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('gerer-acces');

        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in([
                User::ROLE_DG,
                User::ROLE_SECRETAIRE,
            ])],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $donnees['etablissement_id'] = Student::etablissementActifId();

        $utilisateur = User::create($donnees);

        return back()->with('succes', "Accès créé pour {$utilisateur->name} ({$utilisateur->libelleRole()}).");
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        Gate::authorize('gerer-acces');

        $donnees = $request->validate([
            'role' => ['required', Rule::in([
                User::ROLE_DG,
                User::ROLE_SECRETAIRE,
            ])],
        ]);

        abort_unless($utilisateur->etablissement_id === Student::etablissementActifId()
            && ! $utilisateur->estDeveloppeur(), 404);

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

        abort_unless($utilisateur->etablissement_id === Student::etablissementActifId()
            && ! $utilisateur->estDeveloppeur(), 404);

        if ($utilisateur->is($request->user())) {
            return back()->withErrors(['acces' => 'Vous ne pouvez pas supprimer votre propre accès.']);
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
