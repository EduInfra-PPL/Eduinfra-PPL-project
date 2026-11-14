<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserDirectoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_non_admin_cannot_open_page(): void
    {
        $user = User::factory()->create(['role_enum' => 'pelapor_guru', 'is_verified' => true]);

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admin_sees_users(): void
    {
        $admin = User::factory()->create(['role_enum' => 'admin_dinas', 'is_verified' => true]);
        $other = User::factory()->create(['is_verified' => true]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($other->email);
    }

    public function test_login_event_records_activity(): void
    {
        $user = User::factory()->create(['is_verified' => true]);

        event(new Login('web', $user, false));
        event(new Login('web', $user, false));

        $user->refresh();
        $this->assertSame(2, (int) $user->login_count);
        $this->assertNotNull($user->last_login_at);
    }
}