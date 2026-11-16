<?php

namespace Tests\Unit;

use App\Services\Dapodik\CsvSchoolSource;
use App\Services\Dapodik\SchoolRecordMapper;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CsvSchoolSourceTest extends TestCase
{
    private string $tmpFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'schools_');
    }

    protected function tearDown(): void
    {
        @unlink($this->tmpFile);
        parent::tearDown();
    }

    private function mapper(): SchoolRecordMapper
    {
        return new SchoolRecordMapper([
            'npsn' => 'NPSN',
            'name' => 'Nama',
            'educational_level' => 'Bentuk',
            'lat' => 'Lintang',
            'lng' => 'Bujur',
            'region_code' => 'Kode Kab',
            'contact_email' => 'Email',
        ]);
    }

    public function test_reads_rows_maps_fields_and_strips_bom_and_blank_values(): void
    {
        file_put_contents($this->tmpFile, "\xEF\xBB\xBF"
            ."NPSN,Nama,Bentuk,Lintang,Bujur,Kode Kab,Email\n"
            ."10802199,  SDN 02 Sambas  ,MI,1.30,109.30,61.01,\n"
            ."\n"
            ."20300001,SMP Tes,SMP,-8.3,124.4,53.05,guru@example.org\n");

        $rows = iterator_to_array((new CsvSchoolSource($this->tmpFile, $this->mapper()))->records(), false);

        $this->assertCount(2, $rows);
        $this->assertSame('10802199', $rows[0]['npsn']);
        $this->assertSame('SDN 02 Sambas', $rows[0]['name'], 'nilai harus di-trim');
        $this->assertSame('MI', $rows[0]['educational_level']);
        $this->assertSame('61.01', $rows[0]['region_code']);
        $this->assertNull($rows[0]['contact_email'], 'string kosong menjadi null');
        $this->assertSame('guru@example.org', $rows[1]['contact_email']);
    }

    public function test_short_rows_are_padded_with_nulls(): void
    {
        file_put_contents($this->tmpFile, "NPSN,Nama,Bentuk,Lintang,Bujur\n10802199,SDN,SD,1.3\n");

        $rows = iterator_to_array((new CsvSchoolSource($this->tmpFile, $this->mapper()))->records(), false);

        $this->assertCount(1, $rows);
        $this->assertNull($rows[0]['lng']);
        $this->assertNull($rows[0]['region_code']);
    }

    public function test_missing_file_throws_runtime_exception(): void
    {
        $source = new CsvSchoolSource($this->tmpFile.'-tidak-ada', $this->mapper());

        $this->expectException(RuntimeException::class);
        iterator_to_array($source->records(), false);
    }

    public function test_custom_delimiter_is_respected(): void
    {
        file_put_contents($this->tmpFile, "NPSN;Nama\n10802199;SDN Titik;Koma\n");
        $mapper = new SchoolRecordMapper(['npsn' => 'NPSN', 'name' => 'Nama']);

        $rows = iterator_to_array((new CsvSchoolSource($this->tmpFile, $mapper, ';'))->records(), false);

        $this->assertSame('10802199', $rows[0]['npsn']);
        $this->assertSame('SDN Titik', $rows[0]['name']);
    }
}
