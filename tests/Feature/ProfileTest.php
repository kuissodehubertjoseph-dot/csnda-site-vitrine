<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un compte développeur n'est rattaché à aucun établissement précis :
     * ChargerEtablissementActif le laisse passer pour n'importe quel
     * etablissement_id en session, ce qui évite à ces tests génériques
     * (sans lien avec la logique multi-établissement) de dépendre d'un
     * rattachement particulier.
     */
    private function utilisateur(): User
    {
        return User::factory()->create(['role' => User::ROLE_DEVELOPPEUR]);
    }

    private function etablissementId(): int
    {
        return \App\Models\Etablissement::where('slug', 'css')->value('id');
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->utilisateur();

        $response = $this
            ->actingAs($user)
            ->withSession(['etablissement_id' => $this->etablissementId()])
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->utilisateur();

        $response = $this
            ->actingAs($user)
            ->withSession(['etablissement_id' => $this->etablissementId()])
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = $this->utilisateur();

        $response = $this
            ->actingAs($user)
            ->withSession(['etablissement_id' => $this->etablissementId()])
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->utilisateur();

        $response = $this
            ->actingAs($user)
            ->withSession(['etablissement_id' => $this->etablissementId()])
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->utilisateur();

        $response = $this
            ->actingAs($user)
            ->withSession(['etablissement_id' => $this->etablissementId()])
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
