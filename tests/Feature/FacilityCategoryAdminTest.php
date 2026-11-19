<?php

namespace Tests\Feature;

use App\Models\FacilityCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * PBI-03 / FR-03: halaman admin kategori fasilitas dan bobot urgensi, serta API-nya.
 * Setiap tes dibungkus transaksi dan di-rollback, sehingga data di database tidak berubah.
 */
class FacilityCategoryAdminTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::factory()->create(['role_enum' => 'admin_dinas', 'is_verified' => true]);
    }

    private function category(array $over = []): FacilityCategory
    {
        return FacilityCategory::create(array_merge([
            'name' => 'Uji Kategori '.uniqid(),
            'sub_category' => 'Sub Uji',
            'urgency_weight' => 3,
            'default_unit' => 'unit',
        ], $over));
    }

    // ------------------------------------------------------------- akses

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
    }

    public function test_non_admin_is_forbidden(): void
    {
        $user = User::factory()->create(['role_enum' => 'pelapor_guru', 'is_verified' => true]);

        $this->actingAs($user)->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.categories.store'), ['name' => 'X', 'urgency_weight' => 3])->assertForbidden();
    }

    // ------------------------------------------------------------- R: taksonomi

    public function test_admin_sees_category_list_with_filters(): void
    {
        // nama kategori utama tampil juga di dropdown filter, jadi yang diperiksa sub-kategori
        $high = $this->category(['name' => 'Uji Grup', 'sub_category' => 'Uji Atap Runtuh', 'urgency_weight' => 5]);
        $low = $this->category(['name' => 'Uji Grup', 'sub_category' => 'Uji Rak Buku', 'urgency_weight' => 1]);
        $off = $this->category(['name' => 'Uji Grup', 'sub_category' => 'Uji Nonaktif', 'is_enabled' => false]);

        $this->actingAs($this->admin())
            ->get(route('admin.categories.index', ['q' => 'Uji']))
            ->assertOk()
            ->assertSee('Uji Atap Runtuh')
            ->assertSee('Uji Rak Buku')
            ->assertDontSee('Uji Nonaktif');

        $this->get(route('admin.categories.index', ['q' => 'Uji', 'weight' => 5]))
            ->assertSee($high->sub_category)->assertDontSee($low->sub_category);

        $this->get(route('admin.categories.index', ['status' => 'nonaktif', 'q' => 'Uji']))
            ->assertSee($off->sub_category)->assertDontSee($high->sub_category);

        $this->get(route('admin.categories.index', ['id' => $high->id]))
            ->assertSee('Detail Kategori')->assertSee('Ubah Bobot');
    }

    // ------------------------------------------------------------- C: tambah

    public function test_admin_can_add_category(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), [
                'name' => '  Uji Ruang UKS ',
                'sub_category' => 'Ruang dan Perlengkapan',
                'urgency_weight' => 3,
                'default_unit' => 'ruang',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('facility_categories', [
            'name' => 'Uji Ruang UKS',
            'sub_category' => 'Ruang dan Perlengkapan',
            'urgency_weight' => 3,
            'default_unit' => 'ruang',
            'is_enabled' => true,
        ]);
    }

    public function test_sub_category_is_optional(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), ['name' => 'Uji Tanpa Sub', 'urgency_weight' => 2])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('facility_categories', ['name' => 'Uji Tanpa Sub', 'sub_category' => '', 'default_unit' => null]);
    }

    public function test_weight_must_be_between_1_and_5(): void
    {
        $admin = $this->admin();

        foreach ([0, 6, 'tinggi', null] as $weight) {
            $this->actingAs($admin)
                ->post(route('admin.categories.store'), ['name' => 'Uji Bobot', 'urgency_weight' => $weight])
                ->assertSessionHasErrors('urgency_weight');
        }

        $this->assertDatabaseMissing('facility_categories', ['name' => 'Uji Bobot']);
    }

    public function test_duplicate_name_and_sub_category_is_rejected(): void
    {
        $existing = $this->category();

        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), [
                'name' => $existing->name,
                'sub_category' => $existing->sub_category,
                'urgency_weight' => 4,
                '_form' => 'create',
            ])
            ->assertSessionHasErrors('sub_category');

        $this->assertSame(1, FacilityCategory::where('name', $existing->name)->count());
    }

    // ------------------------------------------------------------- U: ubah bobot

    public function test_admin_can_change_urgency_weight_and_unit(): void
    {
        $category = $this->category(['urgency_weight' => 2]);

        $this->actingAs($this->admin())
            ->patch(route('admin.categories.update', $category->id), ['urgency_weight' => 5, 'default_unit' => 'paket'])
            ->assertRedirect(route('admin.categories.index', ['id' => $category->id]))
            ->assertSessionHas('success');

        $category->refresh();
        $this->assertSame(5, $category->urgency_weight);
        $this->assertSame('paket', $category->default_unit);
    }

    public function test_name_and_sub_category_cannot_be_changed(): void
    {
        $category = $this->category();

        $this->actingAs($this->admin())
            ->patch(route('admin.categories.update', $category->id), ['urgency_weight' => 4, 'name' => 'Nama Baru'])
            ->assertSessionHasErrors('name');

        $this->assertSame(3, $category->fresh()->urgency_weight);
    }

    public function test_update_rejects_invalid_weight(): void
    {
        $category = $this->category();

        $this->actingAs($this->admin())
            ->patch(route('admin.categories.update', $category->id), ['urgency_weight' => 9])
            ->assertSessionHasErrors('urgency_weight');
    }

    // ------------------------------------------------------------- D: nonaktifkan

    public function test_admin_can_deactivate_and_reactivate_category(): void
    {
        $category = $this->category();
        $admin = $this->admin();

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->id))->assertSessionHas('success');
        $this->assertFalse($category->fresh()->is_enabled);
        $this->assertDatabaseHas('facility_categories', ['id' => $category->id]); // tidak dihapus

        // kategori nonaktif tidak muncul di formulir laporan
        $this->get(route('reports.create'))->assertDontSee($category->name);

        $this->actingAs($admin)->patch(route('admin.categories.activate', $category->id))->assertSessionHas('success');
        $this->assertTrue($category->fresh()->is_enabled);
    }

    public function test_unknown_category_returns_error_message(): void
    {
        $this->actingAs($this->admin())
            ->delete(route('admin.categories.destroy', 999999999))
            ->assertSessionHas('error');
    }

    // ------------------------------------------------------------- API v1

    public function test_api_lists_only_enabled_by_default(): void
    {
        $on = $this->category();
        $off = $this->category(['is_enabled' => false]);

        $ids = array_column($this->getJson('/api/v1/facility-categories')->assertOk()->json('data'), 'id');
        $this->assertContains($on->id, $ids);
        $this->assertNotContains($off->id, $ids);

        $all = array_column($this->getJson('/api/v1/facility-categories?include_disabled=1')->json('data'), 'id');
        $this->assertContains($off->id, $all);
    }

    public function test_api_create_update_and_disable(): void
    {
        $this->actingAs($this->admin());
        $payload = ['name' => 'Uji API UKS', 'sub_category' => 'Ruang', 'urgency_weight' => 3, 'default_unit' => 'ruang'];

        $id = $this->postJson('/api/v1/facility-categories', $payload)->assertCreated()->json('data.id');
        $this->postJson('/api/v1/facility-categories', $payload)->assertStatus(409);
        $this->postJson('/api/v1/facility-categories', ['name' => 'Uji', 'urgency_weight' => 6])->assertStatus(422);

        $this->patchJson("/api/v1/facility-categories/{$id}", ['urgency_weight' => 5])
            ->assertOk()->assertJsonPath('data.urgency_weight', 5);
        $this->patchJson("/api/v1/facility-categories/{$id}", ['name' => 'Lain'])->assertStatus(422);
        $this->patchJson("/api/v1/facility-categories/{$id}", [])->assertStatus(422);
        $this->patchJson('/api/v1/facility-categories/999999999', ['urgency_weight' => 2])->assertNotFound();

        $this->deleteJson("/api/v1/facility-categories/{$id}")->assertNoContent();
        $this->assertFalse(FacilityCategory::find($id)->is_enabled);
    }
}
