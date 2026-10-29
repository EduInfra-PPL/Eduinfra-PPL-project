<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pengujian API Domain 1 (black box, Subbab 5.3).
 *
 * Prasyarat: database uji PostgreSQL+PostGIS yang sudah dimigrasi dan di-seed
 *   php artisan migrate --env=testing
 *   php artisan db:seed --class=Domain1Seeder --env=testing
 * phpunit.xml: <env name="EDUINFRA_WRITE_MIDDLEWARE" value="(empty)"/> agar endpoint
 * tulis terbuka saat pengujian (auth/RBAC diuji di Domain 5).
 * Setiap tes dibungkus transaksi dan di-rollback, sehingga data seed tidak berubah.
 */
class Domain1ApiTest extends TestCase
{
    use DatabaseTransactions;

    private int $npsnSeq = 98000000;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::table('regions')->where('code', '53.05')->doesntExist()) {
            $this->markTestSkipped('Seed Domain 1 belum dijalankan (php artisan db:seed --class=Domain1Seeder).');
        }
    }

    /** Sekolah baru di dalam kotak Kabupaten Alor (seed). */
    private function schoolPayload(array $over = []): array
    {
        return array_merge([
            'npsn' => (string) (++$this->npsnSeq),
            'name' => 'SDN Uji Alor',
            'educational_level' => 'SD',
            'lat' => -8.30,
            'lng' => 124.40,
        ], $over);
    }

    // ---------------------------------------------------------------- schools

    public function test_school_list_is_public_and_paginated(): void
    {
        $this->getJson('/api/v1/schools?per_page=5')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'npsn', 'name', 'educational_level', 'lat', 'lng', 'region_code', 'is_3t_tier']],
                'meta' => ['page', 'per_page', 'total'],
            ])
            ->assertJsonPath('meta.per_page', 5);
    }

    public function test_school_list_filters_by_3t_level_and_search(): void
    {
        $rows = $this->getJson('/api/v1/schools?is_3t=true&per_page=100')->assertOk()->json('data');
        $this->assertNotEmpty($rows);
        foreach ($rows as $row) {
            $this->assertGreaterThan(0, $row['is_3t_tier']);
        }

        $levels = $this->getJson('/api/v1/schools?level=SMP&per_page=100')->json('data');
        $this->assertNotEmpty($levels);
        $this->assertSame(['SMP'], array_values(array_unique(array_column($levels, 'educational_level'))));

        $this->getJson('/api/v1/schools?q=99000001')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/schools?q=Dummy&region_code=53.05')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_school_list_rejects_invalid_filter(): void
    {
        $this->getJson('/api/v1/schools?level=SMK')->assertStatus(422)->assertJsonValidationErrors('level');
    }

    public function test_store_school_assigns_region_from_coordinates(): void
    {
        $this->postJson('/api/v1/schools', $this->schoolPayload())
            ->assertCreated()
            ->assertJsonPath('data.region_code', '53.05')
            ->assertJsonPath('data.is_active', true);
    }

    public function test_store_school_validates_input(): void
    {
        $this->postJson('/api/v1/schools', $this->schoolPayload(['npsn' => '123']))
            ->assertStatus(422)->assertJsonValidationErrors('npsn');

        $this->postJson('/api/v1/schools', $this->schoolPayload(['educational_level' => 'TK']))
            ->assertStatus(422)->assertJsonValidationErrors('educational_level');

        $this->postJson('/api/v1/schools', $this->schoolPayload(['lat' => 95]))
            ->assertStatus(422)->assertJsonValidationErrors('lat');
    }

    public function test_store_school_outside_any_region_is_rejected(): void
    {
        $this->postJson('/api/v1/schools', $this->schoolPayload(['lat' => 0, 'lng' => 0]))
            ->assertStatus(422)->assertJsonValidationErrors('region_id');
    }

    public function test_duplicate_npsn_returns_conflict(): void
    {
        $payload = $this->schoolPayload();
        $this->postJson('/api/v1/schools', $payload)->assertCreated();
        $this->postJson('/api/v1/schools', $payload)->assertStatus(409);
    }

    public function test_update_only_changes_contact_and_accreditation(): void
    {
        $id = $this->postJson('/api/v1/schools', $this->schoolPayload())->json('data.id');

        $this->putJson("/api/v1/schools/{$id}", ['accreditation' => 'A', 'contact_phone' => '0812-1111-2222'])
            ->assertOk()
            ->assertJsonPath('data.accreditation', 'A')
            ->assertJsonPath('data.contact_phone', '0812-1111-2222');

        $this->putJson("/api/v1/schools/{$id}", ['npsn' => '11111111'])->assertStatus(422);
        $this->putJson("/api/v1/schools/{$id}", ['name' => 'Nama Lain'])->assertStatus(422);
        $this->putJson("/api/v1/schools/{$id}", ['accreditation' => 'Z'])->assertStatus(422);
    }

    public function test_delete_is_soft_delete_and_hides_school(): void
    {
        $payload = $this->schoolPayload();
        $id = $this->postJson('/api/v1/schools', $payload)->json('data.id');

        $this->deleteJson("/api/v1/schools/{$id}")->assertNoContent();
        $this->getJson("/api/v1/schools/{$id}")->assertNotFound();
        $this->deleteJson("/api/v1/schools/{$id}")->assertNotFound();

        $this->assertFalse((bool) DB::table('schools')->where('npsn', $payload['npsn'])->value('is_active'));
    }

    public function test_nearby_orders_by_distance_and_respects_radius(): void
    {
        $a = $this->schoolPayload(['name' => 'Titik A', 'lng' => 124.40]);
        $b = $this->schoolPayload(['name' => 'Titik B', 'lng' => 124.45]); // ~5,5 km dari A
        $this->postJson('/api/v1/schools', $a)->assertCreated();
        $this->postJson('/api/v1/schools', $b)->assertCreated();

        $near = $this->getJson('/api/v1/schools/nearby?lat=-8.30&lng=124.40&radius_m=10000')
            ->assertOk()->json('data');
        $this->assertSame($a['npsn'], trim($near[0]['npsn']));
        $this->assertSame($b['npsn'], trim($near[1]['npsn']));
        $this->assertLessThan(1, $near[0]['dist_meters']);
        $this->assertEqualsWithDelta(5500, $near[1]['dist_meters'], 300);

        $this->getJson('/api/v1/schools/nearby?lat=-8.30&lng=124.40&radius_m=1000')
            ->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_schools_geojson_is_a_feature_collection(): void
    {
        $this->getJson('/api/v1/schools/geojson?bbox=123.9,-8.6,124.9,-8.1')
            ->assertOk()
            ->assertJsonPath('type', 'FeatureCollection')
            ->assertJsonPath('features.0.geometry.type', 'Point');
    }

    public function test_import_upserts_by_npsn_and_maps_levels(): void
    {
        $npsn = (string) (++$this->npsnSeq);
        $payload = ['schools' => [
            ['npsn' => $npsn, 'name' => 'MIS Uji', 'educational_level' => 'MI', 'lat' => -8.30, 'lng' => 124.40],
            ['npsn' => '1', 'name' => 'Salah', 'educational_level' => 'SD', 'lat' => -8.30, 'lng' => 124.40],
            ['npsn' => (string) (++$this->npsnSeq), 'name' => 'PAUD', 'educational_level' => 'PAUD', 'lat' => -8.3, 'lng' => 124.4],
        ]];

        $this->postJson('/api/v1/schools/import', $payload)
            ->assertOk()
            ->assertJsonPath('data.created', 1)
            ->assertJsonPath('data.failed', 2);

        $this->assertSame('SD', trim(DB::table('schools')->where('npsn', $npsn)->value('educational_level')));

        $payload['schools'][0]['name'] = 'MIS Uji (Diperbarui)';
        $this->postJson('/api/v1/schools/import', ['schools' => [$payload['schools'][0]]])
            ->assertOk()
            ->assertJsonPath('data.created', 0)
            ->assertJsonPath('data.updated', 1);
    }

    // ---------------------------------------------------------------- regions

    public function test_region_list_and_filters(): void
    {
        $this->getJson('/api/v1/regions?level=prov')->assertOk()->assertJsonCount(15, 'data');
        $this->getJson('/api/v1/regions?code_prefix=95&level=kab')->assertOk()->assertJsonCount(8, 'data');
        $this->getJson('/api/v1/regions?is_3t=false&level=kab')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_region_lookup_returns_most_specific_first(): void
    {
        $codes = array_column(
            $this->getJson('/api/v1/regions/lookup?lat=-8.30&lng=124.40')->assertOk()->json('data'),
            'code'
        );
        $this->assertSame(['53.05', '53'], $codes);
    }

    public function test_region_detail_contains_polygon(): void
    {
        $id = DB::table('regions')->where('code', '53.05')->value('id');
        $this->getJson("/api/v1/regions/{$id}")
            ->assertOk()
            ->assertJsonPath('data.geometry.type', 'MultiPolygon');
    }

    public function test_region_import_normalizes_polygon_and_upserts(): void
    {
        $geojson = ['type' => 'FeatureCollection', 'features' => [[
            'type' => 'Feature',
            'properties' => ['code' => '99.99', 'name' => 'Wilayah Uji', 'level' => 'kab', 'is_3t_tier' => 1],
            'geometry' => ['type' => 'Polygon', 'coordinates' => [[
                [100.0, 0.0], [100.1, 0.0], [100.1, 0.1], [100.0, 0.1], [100.0, 0.0],
            ]]],
        ]]];

        $this->postJson('/api/v1/regions/import', $geojson)
            ->assertOk()->assertJsonPath('data.created', 1)->assertJsonPath('data.failed', 0);
        $this->postJson('/api/v1/regions/import', $geojson)
            ->assertOk()->assertJsonPath('data.created', 0)->assertJsonPath('data.updated', 1);

        $codes = array_column(
            $this->getJson('/api/v1/regions/lookup?lat=0.05&lng=100.05')->json('data'),
            'code'
        );
        $this->assertSame(['99.99'], $codes);
    }

    public function test_region_import_reports_invalid_features(): void
    {
        $bad = ['type' => 'FeatureCollection', 'features' => [[
            'type' => 'Feature',
            'properties' => ['code' => '99.98', 'name' => 'Salah', 'level' => 'desa'],
            'geometry' => ['type' => 'Point', 'coordinates' => [100.0, 0.0]],
        ]]];

        $this->postJson('/api/v1/regions/import', $bad)
            ->assertOk()->assertJsonPath('data.created', 0)->assertJsonPath('data.failed', 1);
    }

    public function test_region_tier_can_be_updated(): void
    {
        $id = DB::table('regions')->where('code', '53.71')->value('id');

        $this->patchJson("/api/v1/regions/{$id}/tier", ['is_3t_tier' => 2])
            ->assertOk()->assertJsonPath('data.is_3t_tier', 2);
        $this->patchJson("/api/v1/regions/{$id}/tier", ['is_3t_tier' => 3])->assertStatus(422);
    }

    public function test_region_in_use_cannot_be_deleted(): void
    {
        $id = DB::table('regions')->where('code', '53.05')->value('id');
        $this->deleteJson("/api/v1/regions/{$id}")->assertStatus(409);
    }

    public function test_unused_region_can_be_deleted(): void
    {
        $this->postJson('/api/v1/regions/import', ['type' => 'FeatureCollection', 'features' => [[
            'type' => 'Feature',
            'properties' => ['code' => '99.97', 'name' => 'Hapus Saya', 'level' => 'kab'],
            'geometry' => ['type' => 'Polygon', 'coordinates' => [[
                [101.0, 0.0], [101.1, 0.0], [101.1, 0.1], [101.0, 0.1], [101.0, 0.0],
            ]]],
        ]]])->assertOk();

        $id = DB::table('regions')->where('code', '99.97')->value('id');
        $this->deleteJson("/api/v1/regions/{$id}")->assertNoContent();
    }

    // ------------------------------------------------------ facility_categories

    public function test_category_list_returns_enabled_only_by_default(): void
    {
        $rows = $this->getJson('/api/v1/facility-categories')->assertOk()->json('data');
        $this->assertCount(11, $rows);
        $this->assertNotContains(false, array_column($rows, 'is_enabled'));
    }

    public function test_category_weight_must_be_between_1_and_5(): void
    {
        $this->postJson('/api/v1/facility-categories', ['name' => 'Uji', 'urgency_weight' => 6])
            ->assertStatus(422)->assertJsonValidationErrors('urgency_weight');
        $this->postJson('/api/v1/facility-categories', ['name' => 'Uji', 'urgency_weight' => 0])
            ->assertStatus(422);
    }

    public function test_category_create_update_and_disable(): void
    {
        $payload = ['name' => 'Ruang UKS', 'sub_category' => 'Ruang dan Perlengkapan', 'urgency_weight' => 3, 'default_unit' => 'ruang'];

        $id = $this->postJson('/api/v1/facility-categories', $payload)->assertCreated()->json('data.id');
        $this->postJson('/api/v1/facility-categories', $payload)->assertStatus(409);

        $this->patchJson("/api/v1/facility-categories/{$id}", ['urgency_weight' => 5])
            ->assertOk()->assertJsonPath('data.urgency_weight', 5);
        $this->patchJson("/api/v1/facility-categories/{$id}", ['name' => 'Lain'])->assertStatus(422);

        $this->deleteJson("/api/v1/facility-categories/{$id}")->assertNoContent();
        $this->assertFalse((bool) DB::table('facility_categories')->where('id', $id)->value('is_enabled'));

        $ids = array_column($this->getJson('/api/v1/facility-categories')->json('data'), 'id');
        $this->assertNotContains($id, $ids);
        $all = array_column($this->getJson('/api/v1/facility-categories?include_disabled=1')->json('data'), 'id');
        $this->assertContains($id, $all);
    }
}
