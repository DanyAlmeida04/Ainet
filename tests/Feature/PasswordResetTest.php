<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_loads()
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Recuperar Palavra-passe');
    }

    public function test_request_reset_link_with_invalid_email()
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_request_reset_link_sends_notification()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'registered@example.com',
            'user_type' => 'C',
            'gender' => 'M',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'registered@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_page_loads()
    {
        $response = $this->get(route('password.reset', ['token' => 'dummy-token']) . '?email=test@example.com');

        $response->assertStatus(200);
        $response->assertSee('Definir Nova Palavra-passe');
        $response->assertSee('dummy-token');
    }

    public function test_reset_password_submits_successfully()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'registered@example.com',
            'password' => Hash::make('old-password'),
            'user_type' => 'C',
            'gender' => 'M',
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.update_reset'), [
            'token' => $token,
            'email' => 'registered@example.com',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }
}
