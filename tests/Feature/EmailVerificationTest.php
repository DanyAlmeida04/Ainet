<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_customer_cannot_access_checkout()
    {
        // Create an unverified customer (email_verified_at is null by default in factory or we can force it)
        $user = User::factory()->create([
            'user_type' => 'C',
            'gender' => 'M',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)
                         ->get(route('cart.payment'));

        // Should be redirected to the email verification notice page
        $response->assertRedirect(route('verification.notice'));
    }

    public function test_verified_customer_can_access_checkout()
    {
        $user = User::factory()->create([
            'user_type' => 'C',
            'gender' => 'F',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
                         ->get(route('cart.payment'));

        // Should allow access (returns 200)
        $response->assertStatus(200);
    }

    public function test_registration_triggers_email_verification_event()
    {
        Event::fake();

        $response = $this->post(route('register'), [
            'name' => 'Verify User',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nif' => '123456789',
            'address' => 'Rua do Teste',
            'gender' => 'F',
        ]);

        Event::assertDispatched(Registered::class);
    }

    public function test_customer_can_verify_email_via_signed_route()
    {
        $user = User::factory()->create([
            'user_type' => 'C',
            'gender' => 'M',
            'email_verified_at' => null,
        ]);

        $this->assertNull($user->email_verified_at);

        // Generate a signed verification URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user)
                         ->get($verificationUrl);

        $response->assertRedirect(route('catalog.index'));
        $response->assertSessionHas('success', 'E-mail verificado com sucesso! Já pode concluir a sua compra.');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
