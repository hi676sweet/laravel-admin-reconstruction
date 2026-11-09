<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_and_cannot_create_tickets(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/customers')->assertRedirect(route('login'));
        $this->get('/tickets')->assertRedirect(route('login'));
        $this->post('/tickets', [])->assertRedirect(route('login'));
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_operator_can_sign_in_and_sign_out(): void
    {
        $operator = User::factory()->create(['email' => 'operator@example.test', 'password' => 'test-secret-strong']);
        $this->post('/login', ['email' => $operator->email, 'password' => 'test-secret-strong'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($operator);
        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_wrong_credentials_do_not_create_an_authenticated_session(): void
    {
        User::factory()->create(['email' => 'operator@example.test']);
        $this->post('/login', ['email' => 'operator@example.test', 'password' => 'incorrect'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_repeated_login_attempts_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post('/login', ['email' => 'absent@example.test', 'password' => 'incorrect'])->assertStatus(302);
        }
        $this->post('/login', ['email' => 'absent@example.test', 'password' => 'incorrect'])->assertTooManyRequests();
    }
}
