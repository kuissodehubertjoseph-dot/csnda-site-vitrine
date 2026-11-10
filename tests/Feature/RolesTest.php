<?php

namespace Tests\Feature;

use App\Models\Etablissement;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'nouveaudev@test.bj']);
    }

    public function test_le_seed_cree_un_compte_global_avec_les_identifiants_configures(): void
    {
        config([
            'administration.email' => 'cortex-global@test.bj',
            'administration.password' => 'Motdepasse1',
        ]);

        app(AdminUserSeeder::class)->run();

        $compte = User::where('email', 'cortex-global@test.bj')->firstOrFail();

        $this->assertSame(User::ROLE_DEVELOPPEUR, $compte->role);
        $this->assertNull($compte->etablissement_id);
        $this->assertTrue(Hash::check('Motdepasse1', $compte->password));
    }

    public function test_le_seed_met_a_jour_le_compte_global_existant(): void
    {
        $compte = User::factory()->create([
            'name' => 'Administration Cortex Bénin TV',
            'email' => 'ancien-global@test.bj',
            'role' => User::ROLE_DEVELOPPEUR,
            'etablissement_id' => null,
        ]);

        config([
            'administration.email' => 'nouveau-global@test.bj',
            'administration.password' => 'Motdepasse2',
        ]);

        app(AdminUserSeeder::class)->run();

        $compte->refresh();
        $this->assertSame('nouveau-global@test.bj', $compte->email);
        $this->assertSame(User::ROLE_DEVELOPPEUR, $compte->role);
        $this->assertNull($compte->etablissement_id);
        $this->assertTrue(Hash::check('Motdepasse2', $compte->password));
        $this->assertDatabaseMissing('users', ['email' => 'ancien-global@test.bj']);
    }

    public function test_un_compte_ne_peut_pas_etre_promu_developpeur_depuis_les_acces(): void
    {
        $this->agir($this->dg)
            ->patch("/acces/{$this->secretaire->id}", ['role' => User::ROLE_DEVELOPPEUR])
            ->assertSessionHasErrors('role');

        $this->assertSame(User::ROLE_SECRETAIRE, $this->secretaire->fresh()->role);
        $this->assertSame($this->etablissement->id, $this->secretaire->fresh()->etablissement_id);
    }

    public function test_le_dg_ne_peut_pas_modifier_un_compte_developpeur(): void
    {
        $this->agir($this->dg)
            ->patch("/acces/{$this->developpeur->id}", ['role' => User::ROLE_SECRETAIRE])
            ->assertNotFound();

        $this->assertSame(User::ROLE_DEVELOPPEUR, $this->developpeur->fresh()->role);
    }

    public function test_le_dg_ne_peut_pas_supprimer_un_compte_developpeur(): void
    {
        $this->agir($this->dg)
            ->delete("/acces/{$this->developpeur->id}")
            ->assertNotFound();

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
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'dev2@test.bj']);
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

    public function test_la_page_des_acces_ne_revele_pas_les_comptes_ni_le_role_developpeur(): void
    {
        $reponse = $this->agir($this->dg)->get('/acces');

        $reponse->assertOk();
        $reponse->assertDontSee($this->developpeur->email);
        $reponse->assertDontSee('Développeur');
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

    public function test_l_assistant_accede_a_toutes_les_ecoles_sans_pouvoir_supprimer(): void
    {
        $assistant = User::factory()->create(['role' => User::ROLE_ASSISTANT, 'etablissement_id' => null]);

        $autre = Etablissement::where('id', '!=', $this->etablissement->id)->first()
            ?? Etablissement::create(['slug' => 'autre', 'nom' => 'Autre', 'statut' => 'actif']);

        $this->assertTrue($assistant->peutAccederA($this->etablissement));
        $this->assertTrue($assistant->peutAccederA($autre));

        $this->agir($assistant)->get('/eleves')->assertOk();
        $this->agir($assistant)->delete("/eleves/{$this->eleve->id}")->assertForbidden();
        $this->agir($assistant)->delete('/eleves-tout')->assertForbidden();
        $this->agir($assistant)->get('/acces')->assertForbidden();

        $this->assertDatabaseHas('students', ['id' => $this->eleve->id]);
    }
}
