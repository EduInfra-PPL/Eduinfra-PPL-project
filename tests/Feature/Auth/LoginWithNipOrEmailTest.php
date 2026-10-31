<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PBI-13 (Login): login dengan NIP/NIK atau email kedinasan.
 * Catatan: migrasi Domain 1 memakai PostGIS, jalankan test dengan database PostgreSQL.
 */
class LoginWithNipOrEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_shows_the_nip_or_email_field(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('NIP / Email Kedinasan')
            ->assertSee('Kata Sandi Kedinasan');
    }

    public function test_user_can_login_with_email(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_nip(): void
    {
        $user = User::factory()->create(['identity_number' => '198402112008011002']);

        $this->post(route('login.store'), ['email' => '198402112008011002', 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected_with_a_clear_message(): void
    {
        $user = User::factory()->create(['identity_number' => '198402112008011002']);

        $this->post(route('login.store'), ['email' => '198402112008011002', 'password' => 'salah'])
            ->assertSessionHasErrors(['email' => 'NIP/email atau kata sandi tidak sesuai dengan data kedinasan kami.']);

        $this->assertGuest();
    }

    public function test_unverified_account_cannot_login(): void
    {
        $user = User::factory()->create(['is_verified' => false]);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'))->assertRedirect('/');

        $this->assertGuest();
    }
}
