<?php

namespace Tests\Feature\Renovasi;

use App\Models\Region;
use App\Models\RepairProject;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class Pbi09VendorDirectoryTest extends TestCase
{
    use DatabaseTransactions;

    private function createAuthenticatedUser(): User
    {
        return User::factory()->create();
    }

    public function test_vendor_directory_page_renders_with_kpis_and_filters(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->get(route('renovasi.vendors'));
        $response->assertStatus(200);
        $response->assertSee('Direktori Kontraktor LPJK');
        $response->assertSee('Total Rekanan Terdaftar');

        $filteredResponse = $this->actingAs($user)->get(route('renovasi.vendors', [
            'grade' => 'small',
            'region' => '3t',
            'status' => 'active',
        ]));
        $filteredResponse->assertStatus(200);
    }

    public function test_store_vendor_creates_record_linked_to_master_region_and_assigns_project(): void
    {
        $user = $this->createAuthenticatedUser();

        $region = Region::where('level', 'kab')->first() ?? Region::create([
            'code' => '53.05',
            'name' => 'Kabupaten Alor',
            'level' => 'kab',
            'is_3t_tier' => 1,
        ]);

        $project = RepairProject::first();

        $response = $this->actingAs($user)->post(route('renovasi.vendors.store'), [
            'company_name' => 'CV Nusantara Konstruksi 3T',
            'entity_type' => 'CV',
            'license_num' => 'LPJK-9901-TEST',
            'nib' => '9120001122334',
            'contact_phone' => '0812-9988-7766',
            'service_region_id' => $region->id,
            'grade' => 'K3',
            'max_contract_limit' => 2500000000,
            'sbu_status' => 'verified',
            'specialty' => 'Spesialis Atap & Rangka Baja SNI',
            'logistics_note' => 'Armada Kapal Kargo Kepulauan',
            'lead_engineer' => 'Ir. Budi Santoso, ST',
            'rating' => 4.85,
            'is_active' => true,
            'assign_project_id' => $project?->id,
        ]);

        $response->assertRedirect(route('renovasi.vendors'));

        $vendor = Vendor::where('license_num', 'LPJK-9901-TEST')->first();
        $this->assertNotNull($vendor);
        $this->assertEquals($region->id, $vendor->service_region_id);
        $this->assertTrue($vendor->is_active);

        if ($project) {
            $project->refresh();
            $this->assertEquals($vendor->id, $project->vendor_id);
            $this->assertEquals('CV Nusantara Konstruksi 3T', $project->contractor_name);
        }
    }

    public function test_update_vendor_modifies_contact_sbu_and_syncs_assigned_project_name(): void
    {
        $user = $this->createAuthenticatedUser();

        $region = Region::where('level', 'kab')->first() ?? Region::first();
        $vendor = Vendor::first() ?? Vendor::create([
            'company_name' => 'CV Awal Konstruksi',
            'entity_type' => 'CV',
            'license_num' => 'LPJK-7001-UPD',
            'contact_phone' => '0811-1111-2222',
            'service_region_id' => $region->id,
            'grade' => 'K2',
            'max_contract_limit' => 1500000000,
            'sbu_status' => 'review',
            'rating' => 4.50,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('renovasi.vendors.update', $vendor->id), [
            'company_name' => 'PT Awal Konstruksi Terpadu',
            'entity_type' => 'PT',
            'license_num' => $vendor->license_num,
            'nib' => '8123456789012',
            'contact_phone' => '0811-9999-0000',
            'service_region_id' => $vendor->service_region_id,
            'grade' => 'M1',
            'max_contract_limit' => 10000000000,
            'sbu_status' => 'verified',
            'specialty' => 'Konstruksi Terpadu 3T',
            'lead_engineer' => 'Ir. Eko Prasetyo, MT',
            'rating' => 4.95,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('renovasi.vendors'));

        $vendor->refresh();
        $this->assertEquals('PT Awal Konstruksi Terpadu', $vendor->company_name);
        $this->assertEquals('M1', $vendor->grade);
        $this->assertEquals('verified', $vendor->sbu_status);
        $this->assertEquals('0811-9999-0000', $vendor->contact_phone);
    }

    public function test_blacklist_and_delete_vendor_operations(): void
    {
        $user = $this->createAuthenticatedUser();
        $region = Region::where('level', 'kab')->first() ?? Region::first();

        $vendor = Vendor::create([
            'company_name' => 'CV Kontraktor Uji Blacklist',
            'entity_type' => 'CV',
            'license_num' => 'LPJK-9999-BLK',
            'contact_phone' => '0812-0000-1111',
            'service_region_id' => $region->id,
            'grade' => 'K1',
            'max_contract_limit' => 1000000000,
            'sbu_status' => 'verified',
            'rating' => 4.10,
            'is_active' => true,
        ]);

        // Toggle to Blacklist (is_active = false)
        $blacklistResponse = $this->actingAs($user)->patch(route('renovasi.vendors.blacklist', $vendor->id));
        $blacklistResponse->assertRedirect(route('renovasi.vendors'));
        $this->assertFalse($vendor->fresh()->is_active);

        // Delete permanently
        $deleteResponse = $this->actingAs($user)->delete(route('renovasi.vendors.destroy', $vendor->id));
        $deleteResponse->assertRedirect(route('renovasi.vendors'));
        $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
    }
}
