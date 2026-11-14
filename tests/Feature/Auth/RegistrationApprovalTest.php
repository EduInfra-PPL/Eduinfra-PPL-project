<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationApprovalTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::factory()->create(['role_enum' => 'admin_dinas', 'is_verified' => true]);
    }

    private function applicant(): User
    {
        return User::factory()->create([
            'role_enum' => 'auditor_relawan',
            'registration_role' => 'verifikator_3t',
            'is_verified' => false,
        ]);
    }

    public function test_non_admin_cannot_open_page(): void
    {
        $user = User::factory()->create(['role_enum' => 'pelapor_guru', 'is_verified' => true]);

        $this->actingAs($user)->get(route('admin.registrations.index'))->assertForbidden();
    }

    public function test_admin_sees_pending_applicant(): void
    {
        $applicant = $this->applicant();

        $this->actingAs($this->admin())
            ->get(route('admin.registrations.index'))
            ->assertOk()
            ->assertSee($applicant->email);
    }

    public function test_admin_can_approve(): void
    {
        $admin = $this->admin();
        $applicant = $this->applicant();

        $this->actingAs($admin)->post(route('admin.registrations.approve', $applicant))
            ->assertSessionHas('status');

        $applicant->refresh();
        $this->assertTrue((bool) $applicant->is_verified);
        $this->assertNotNull($applicant->approved_at);
        $this->assertSame($admin->id, (int) $applicant->reviewed_by);
    }

    public function test_admin_can_reject_with_reason(): void
    {
        $applicant = $this->applicant();

        $this->actingAs($this->admin())
            ->post(route('admin.registrations.reject', $applicant), ['rejection_reason' => 'NIP tidak ditemukan di BKN'])
            ->assertSessionHas('status');

        $applicant->refresh();
        $this->assertFalse((bool) $applicant->is_verified);
        $this->assertNotNull($applicant->rejected_at);
        $this->assertSame('NIP tidak ditemukan di BKN', $applicant->rejection_reason);
    }

    public function test_reject_requires_reason(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.registrations.reject', $this->applicant()), ['rejection_reason' => ''])
            ->assertSessionHasErrors('rejection_reason');
    }
}