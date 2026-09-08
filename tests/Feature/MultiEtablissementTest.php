<?php

namespace Tests\Feature;

use App\Models\Etablissement;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiEtablissementTest extends TestCase
{
    use RefreshDatabase;

    public function test_connexion_dediee_isole_les_eleves_par_etablissement(): void
    {
        $css = Etablissement::where('slug', 'css')->firstOrFail();
        $ucao = Etablissement::where('slug', 'ucao')->firstOrFail();

        $eleveCss = Student::factory()->create(['etablissement_id' => $css->id, 'classe' => '4e A']);
        $eleveUcao = Student::factory()->create(['etablissement_id' => $ucao->id, 'classe' => 'Licence 1']);

        $dgUcao = User::factory()->create([
            'role' => User::ROLE_DG,
            'etablissement_id' => $ucao->id,
        ]);

        $reponse = $this->actingAs($dgUcao)
            ->withSession(['etablissement_id' => $ucao->id])
            ->get('/eleves');

        $reponse->assertOk();
        $reponse->assertSee($eleveUcao->nom);
        $reponse->assertDontSee($eleveCss->nom);
    }

    public function test_un_utilisateur_ne_peut_pas_acceder_a_un_autre_etablissement(): void
    {
        $css = Etablissement::where('slug', 'css')->firstOrFail();
        $ucao = Etablissement::where('slug', 'ucao')->firstOrFail();

        $dgCss = User::factory()->create([
            'role' => User::ROLE_DG,
            'etablissement_id' => $css->id,
        ]);

        // Un DG rattaché à CSS mais dont la session pointe vers UCAO doit
        // être déconnecté par ChargerEtablissementActif : peutAccederA()
        // refuse cette combinaison.
        $reponse = $this->actingAs($dgCss)
            ->withSession(['etablissement_id' => $ucao->id])
            ->get('/eleves');

        $reponse->assertRedirect(route('accueil'));
        $this->assertGuest();
    }

    public function test_un_eleve_dun_autre_etablissement_est_introuvable(): void
    {
        $css = Etablissement::where('slug', 'css')->firstOrFail();
        $ucao = Etablissement::where('slug', 'ucao')->firstOrFail();

        $eleveUcao = Student::factory()->create(['etablissement_id' => $ucao->id]);

        $dgCss = User::factory()->create([
            'role' => User::ROLE_DG,
            'etablissement_id' => $css->id,
        ]);

        $this->actingAs($dgCss)
            ->withSession(['etablissement_id' => $css->id])
            ->get("/eleves/{$eleveUcao->id}/edit")
            ->assertNotFound();
    }

    public function test_le_developpeur_peut_se_connecter_a_nimporte_quel_etablissement(): void
    {
        $ucao = Etablissement::where('slug', 'ucao')->firstOrFail();

        $developpeur = User::factory()->create(['role' => User::ROLE_DEVELOPPEUR]);

        $this->actingAs($developpeur)
            ->withSession(['etablissement_id' => $ucao->id])
            ->get('/eleves')
            ->assertOk();
    }

    public function test_la_page_de_connexion_par_ecole_fonctionne(): void
    {
        $ucao = Etablissement::where('slug', 'ucao')->firstOrFail();

        $this->get(route('ecoles.connexion', 'ucao'))->assertOk();

        $dgUcao = User::factory()->create([
            'role' => User::ROLE_DG,
            'etablissement_id' => $ucao->id,
            'password' => 'Motdepasse1',
        ]);

        $reponse = $this->post(route('ecoles.connexion', 'ucao'), [
            'email' => $dgUcao->email,
            'password' => 'Motdepasse1',
        ]);

        $reponse->assertRedirect(route('eleves.index'));
        $this->assertAuthenticatedAs($dgUcao);
        $this->assertSame($ucao->id, session('etablissement_id'));
    }

    public function test_la_connexion_est_refusee_pour_un_compte_dune_autre_ecole(): void
    {
        $css = Etablissement::where('slug', 'css')->firstOrFail();

        $dgCss = User::factory()->create([
            'role' => User::ROLE_DG,
            'etablissement_id' => $css->id,
            'password' => 'Motdepasse1',
        ]);

        $reponse = $this->post(route('ecoles.connexion', 'ucao'), [
            'email' => $dgCss->email,
            'password' => 'Motdepasse1',
        ]);

        $reponse->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_les_nouveaux_comptes_sont_rattaches_a_letablissement_actif(): void
    {
        $ucao = Etablissement::where('slug', 'ucao')->firstOrFail();

        $dgUcao = User::factory()->create([
            'role' => User::ROLE_DG,
            'etablissement_id' => $ucao->id,
        ]);

        $this->actingAs($dgUcao)
            ->withSession(['etablissement_id' => $ucao->id])
            ->post('/acces', [
                'name' => 'Secrétaire UCAO',
                'email' => 'secretaire.ucao@test.bj',
                'role' => User::ROLE_SECRETAIRE,
                'password' => 'Motdepasse1',
                'password_confirmation' => 'Motdepasse1',
            ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'secretaire.ucao@test.bj',
            'etablissement_id' => $ucao->id,
        ]);
    }
}
