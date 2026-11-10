<?php

namespace Tests\Unit;

use App\Services\Osm\OsmSchoolMapper;
use PHPUnit\Framework\TestCase;

class OsmSchoolMapperTest extends TestCase
{
    private OsmSchoolMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new OsmSchoolMapper;
    }

    public function test_node_maps_to_record_with_prefixed_osm_id_and_level_from_name(): void
    {
        $result = $this->mapper->toRecord([
            'type' => 'node', 'id' => 123, 'lat' => -8.2745, 'lon' => 124.4507,
            'tags' => ['name' => 'SMP Negeri Wolwal', 'addr:city' => 'Alor', 'phone' => ' 0380-1 '],
        ]);

        $this->assertNull($result['reason']);
        $this->assertSame('n123', $result['record']['osm_id']);
        $this->assertSame('SMP', $result['record']['educational_level']);
        $this->assertSame(-8.2745, $result['record']['lat']);
        $this->assertSame(124.4507, $result['record']['lng']);
        $this->assertSame('Alor', $result['record']['address']);
        $this->assertSame('0380-1', $result['record']['contact_phone']);
    }

    public function test_way_uses_center_coordinates(): void
    {
        $result = $this->mapper->toRecord([
            'type' => 'way', 'id' => 9, 'center' => ['lat' => -6.2, 'lon' => 106.8],
            'tags' => ['name' => 'SDN 01 Menteng'],
        ]);

        $this->assertSame('w9', $result['record']['osm_id']);
        $this->assertSame('SD', $result['record']['educational_level']);
        $this->assertSame(-6.2, $result['record']['lat']);
    }

    public function test_level_falls_back_to_isced_when_name_is_unknown(): void
    {
        $this->assertSame('SMA', $this->mapper->educationalLevel('Sekolah Harapan', ['isced:level' => '3;4']));
        $this->assertSame('SD', $this->mapper->educationalLevel('Sekolah Harapan', ['isced:level' => '1']));
        $this->assertNull($this->mapper->educationalLevel('Sekolah Harapan', []));
    }

    public function test_preschool_and_special_schools_are_skipped(): void
    {
        $this->assertNull($this->mapper->educationalLevel('TK Ceria', []));
        $this->assertNull($this->mapper->educationalLevel('PAUD Melati', ['isced:level' => '0']));
        $this->assertNull($this->mapper->educationalLevel('SLB Negeri 1', []));
    }

    public function test_madrasah_names_are_recognised(): void
    {
        $this->assertSame('SD', $this->mapper->educationalLevel('MIS Nurul Islamiyah', []));
        $this->assertSame('SMP', $this->mapper->educationalLevel('MTsN 1 Kota', []));
        $this->assertSame('SMA', $this->mapper->educationalLevel('MAN 2 Model', []));
        $this->assertSame('SMA', $this->mapper->educationalLevel('SMK Negeri 3', []));
    }

    public function test_missing_name_or_coordinates_are_reported(): void
    {
        $this->assertSame('tanpa_nama', $this->mapper->toRecord(['type' => 'node', 'id' => 1, 'lat' => -8, 'lon' => 124, 'tags' => []])['reason']);
        $this->assertSame('koordinat_tidak_valid', $this->mapper->toRecord(['type' => 'way', 'id' => 2, 'tags' => ['name' => 'SD Tanpa Titik']])['reason']);
    }

    public function test_coordinates_outside_indonesia_are_rejected(): void
    {
        $result = $this->mapper->toRecord([
            'type' => 'node', 'id' => 3, 'lat' => 51.5, 'lon' => -0.1, 'tags' => ['name' => 'SD London'],
        ]);

        $this->assertSame('koordinat_tidak_valid', $result['reason']);
    }

    public function test_invalid_email_is_dropped_and_address_prefers_addr_full(): void
    {
        $this->assertNull($this->mapper->email(['email' => 'bukan-email']));
        $this->assertSame('guru@example.org', $this->mapper->email(['contact:email' => 'guru@example.org']));
        $this->assertSame('Jl. Merdeka 1', $this->mapper->address(['addr:full' => 'Jl. Merdeka 1', 'addr:city' => 'X']));
        $this->assertNull($this->mapper->address([]));
    }
}
