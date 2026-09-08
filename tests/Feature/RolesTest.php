<?php

namespace Tests\Feature;

use App\Models\Etablissement;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    private Etablissement $etablissement;

    private User $developpeur;

    private User $dg;

    private User $secretaire;

    private Student $eleve;

    protected function setUp(): void
    {
        parent::setUp();

        // La migration create_etablissements_table sème toujours la ligne CSS.
        $this->etablissement = Etablissement::where('slug', 'css')->firstOrFail();

        $this->developpeur = User::factory()->create(['role' => User::ROLE_DEVELOPPEUR]);
        $this->dg = User::factory()->create(['role' => User::ROLE_DG, 'etablissement_id' => $this->etablissement->id]);
        $this->secretaire = User::factory()->create(['role' => User::ROLE_SECRETAIRE, 'etablissement_id' => $this->etablissement->id]);
        $this->eleve = Student::factory()->create(['classe' => '4e A', 'etablissement_id' => $this->etablissement->id]);
    }

    /**
     * Simule une connexion déjà rattachée à l'établissement actif : nécessaire
     * depuis le passage au multi-établissement, où ChargerEtablissementActif
     * exige un etablissement_id en session pour laisser passer une requête
     * authentifiée.
     */
    private function agir(User $utilisateur): self
    {
        return $this->actingAs($utilisateur)
            ->withSession(['etablissement_id' => $utilisateur->etablissement_id ?? $this->etablissement->id]);
    }

    public function test_la_secretaire_accede_a_ses_ecrans(): void
    {
        $this->agir($this->secretaire)->get('/eleves')->assertOk();
        $this->agir($this->secretaire)->get('/eleves/create')->assertOk();
        $this->agir($this->secretaire)->get('/eleves/import')->assertOk();
        $this->agir($this->secretaire)->get('/cartes/lot')->assertOk();
        $this->agir($this->secretaire)->get("/eleves/{$this->eleve->id}/edit")->assertOk();
    }

    public function test_la_secretaire_ne_peut_pas_supprimer_un_eleve(): void
    {
        $this->agir($this->secretaire)
            ->delete("/eleves/{$this->eleve->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('students', ['id' => $this->eleve->id]);
    }

    public function test_la_secretaire_ne_peut_pas_tout_supprimer(): void
    {
        $this->agir($this->secretaire)
            ->delete('/eleves-tout', ['confirmation' => 'SUPPRIMER'])
            ->assertForbidden();

        $this->assertSame(1, Student::count());
    }

    public function test_la_secretaire_ne_peut_pas_gerer_les_acces(): void
    {
        $this->agir($this->secretaire)->get('/acces')->assertForbidden();

        $this->agir($this->secretaire)->post('/acces', [
            'name' => 'Intrus',
            'email' => 'intrus@test.bj',
            'role' => User::ROLE_DG,
            'password' => 'Motdepasse1',
            'password_confirmation' => 'Motdepasse1',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'intrus@test.bj']);
    }

    public function test_la_secretaire_peut_deposer_signature_et_cachet(): void
    {
        $this->agir($this->secretaire)->get('/parametres')->assertOk();
    }

    public function test_tous_les_roles_peuvent_deplacer_un_eleve(): void
    {
        $cibles = [
            [$this->secretaire, '3ème A'],
            [$this->dg, '4e B'],
            [$this->developpeur, '5ème C'],
        ];

        foreach ($cibles as [$utilisateur, $classe]) {
            $this->agir($utilisateur)
                ->patch("/eleves/{$this->eleve->id}/classe", ['classe' => $classe])
                ->assertRedirect();

            $this->assertSame($classe, $this->eleve->fresh()->classe);
        }
    }

    public function test_le_deplacement_refuse_une_classe_inconnue(): void
    {
        $this->agir($this->secretaire)
            ->patch("/eleves/{$this->eleve->id}/classe", ['classe' => 'Classe inexistante'])
            ->assertSessionHasErrors('classe');

        $this->assertSame('4e A', $this->eleve->fresh()->classe);
    }

    public function test_le_developpeur_a_tous_les_droits(): void
    {
        $this->agir($this->developpeur)->get('/eleves')->assertOk();
        $this->agir($this->developpeur)->get('/acces')->assertOk();
        $this->agir($this->developpeur)->get('/parametres')->assertOk();
        $this->agir($this->developpeur)->delete("/eleves/{$this->eleve->id}")->assertRedirect();
    }

    public function test_le_dg_peut_creer_un_compte_developpeur(): void
    {
        $this->agir($this->dg)->post('/acces', [
            'name' => 'Nouveau dev',
            'email' => 'nouveaudev@test.bj',
            'role' => User::ROLE_DEVELOPPEUR,
            'password' => 'Motdepasse1',
            'password_confirmation' => 'Motdepasse1',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'nouveaudev@test.bj',
            'role' => User::ROLE_DEVELOPPEUR,
        ]);
    }

    public function test_le_dg_ne_peut_pas_modifier_un_compte_developpeur(): void
    {
        $this->agir($this->dg)
            ->patch("/acces/{$this->developpeur->id}", ['role' => User::ROLE_SECRETAIRE])
            ->assertSessionHasErrors('role');

        $this->assertSame(User::ROLE_DEVELOPPEUR, $this->developpeur->fresh()->role);
    }

    public function test_le_dg_ne_peut_pas_supprimer_un_compte_developpeur(): void
    {
        $this->agir($this->dg)
            ->delete("/acces/{$this->developpeur->id}")
            ->assertSessionHasErrors('acces');

        $this->assertDatabaseHas('users', ['id' => $this->developpeur->id]);
    }

    public function test_le_developpeur_peut_creer_un_autre_developpeur(): void
    {
        $this->agir($this->developpeur)->post('/acces', [
            'name' => 'Dev deux',
            'email' => 'dev2@test.bj',
            'role' => User::ROLE_DEVELOPPEUR,
            'password' => 'Motdepasse1',
            'password_confirmation' => 'Motdepasse1',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'dev2@test.bj',
            'role' => User::ROLE_DEVELOPPEUR,
        ]);
    }

    public function test_le_dg_accede_a_tout(): void
    {
        $this->agir($this->dg)->get('/eleves')->assertOk();
        $this->agir($this->dg)->get('/acces')->assertOk();
        $this->agir($this->dg)->get('/parametres')->assertOk();
    }

    public function test_le_dg_peut_creer_un_acces_secretaire(): void
    {
        $this->agir($this->dg)->post('/acces', [
            'name' => 'Nouvelle Secrétaire',
            'email' => 'nouvelle@test.bj',
            'role' => User::ROLE_SECRETAIRE,
            'password' => 'Motdepasse1',
            'password_confirmation' => 'Motdepasse1',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'nouvelle@test.bj',
            'role' => User::ROLE_SECRETAIRE,
        ]);
    }

    public function test_le_dernier_dg_ne_peut_pas_etre_retrograde(): void
    {
        $this->agir($this->dg)
            ->patch("/acces/{$this->dg->id}", ['role' => User::ROLE_SECRETAIRE])
            ->assertSessionHasErrors('role');

        $this->assertSame(User::ROLE_DG, $this->dg->fresh()->role);
    }

    public function test_le_dg_ne_peut_pas_supprimer_son_propre_acces(): void
    {
        $this->agir($this->dg)
            ->delete("/acces/{$this->dg->id}")
            ->assertSessionHasErrors('acces');

        $this->assertDatabaseHas('users', ['id' => $this->dg->id]);
    }
}
