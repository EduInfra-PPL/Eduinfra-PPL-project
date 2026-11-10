<?php

namespace Tests\Unit;

use App\Services\ApiIndonesia\SchoolDirectoryMapper;
use PHPUnit\Framework\TestCase;

class SchoolDirectoryMapperTest extends TestCase
{
    private function row(array $over = []): array
    {
        return array_merge([
            'npsn' => '20219557',
            'name' => 'SMA NEGERI 3 BANDUNG',
            'jenis' => 'SMA',
            'status' => 'Negeri',
            'province_id' => '32',
            'regency_id' => '3273',
            'address' => 'Jl. Belitung No. 8',
            'lat' => -6.9106,
            'lng' => 107.6183,
            'phone' => '0224235154',
            'email' => null,
            'accreditation' => 'A',
        ], $over);
    }

    public function test_maps_sample_row_to_internal_keys(): void
    {
        $result = (new SchoolDirectoryMapper)->toRecord($this->row());

        $this->assertNull($result['reason']);
        $this->assertSame('20219557', $result['record']['npsn']);
        $this->assertSame('SMA NEGERI 3 BANDUNG', $result['record']['name']);
        $this->assertSame('SMA', $result['record']['educational_level']);
        $this->assertSame('32.73', $result['record']['region_code']);
        $this->assertSame(-6.9106, $result['record']['lat']);
        $this->assertSame('A', $result['record']['accreditation']);
        $this->assertNull($result['record']['contact_email']);
        $this->assertSame('0224235154', $result['record']['contact_phone']);
    }

    public function test_smk_is_mapped_to_sma_and_slb_is_skipped(): void
    {
        $mapper = new SchoolDirectoryMapper;

        $this->assertSame('SMA', $mapper->toRecord($this->row(['jenis' => 'SMK']))['record']['educational_level']);
        $this->assertSame('jenjang_tidak_didukung', $mapper->toRecord($this->row(['jenis' => 'SLB']))['reason']);
    }

    public function test_invalid_npsn_and_region_code_are_reported(): void
    {
        $mapper = new SchoolDirectoryMapper;

        $this->assertSame('npsn_tidak_valid', $mapper->toRecord($this->row(['npsn' => '123']))['reason']);
        $this->assertSame('kode_wilayah_tidak_valid', $mapper->toRecord($this->row(['regency_id' => '']))['reason']);
    }

    public function test_unknown_accreditation_becomes_null_and_invalid_email_is_dropped(): void
    {
        $result = (new SchoolDirectoryMapper)->toRecord($this->row([
            'accreditation' => 'Belum terakreditasi',
            'email' => 'bukan-email',
        ]));

        $this->assertNull($result['record']['accreditation']);
        $this->assertNull($result['record']['contact_email']);
    }
}
