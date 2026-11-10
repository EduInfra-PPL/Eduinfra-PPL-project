<?php

namespace App\Console\Commands;

use App\Services\Osm\OsmSchoolImporter;
use App\Services\Osm\OverpassClient;
use App\Services\Dapodik\CsvSchoolSource;
use App\Services\Dapodik\DapodikWebserviceSource;
use App\Services\Dapodik\SchoolRecordMapper;
use App\Services\Dapodik\SchoolSource;
use App\Services\Schools\SchoolImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Sinkronisasi data sekolah dari sumber eksternal (CSV ekspor Dapodik atau webservice Dapodik).
 * Upsert berdasarkan NPSN lewat SchoolImporter yang sama dengan endpoint impor API.
 */
class SyncSchoolsCommand extends Command
{
    protected $signature = 'schools:sync
        {--source=csv : Sumber data: csv, dapodik, atau osm}
        {--path= : Lokasi berkas CSV (menimpa DAPODIK_CSV_PATH)}
        {--chunk=500 : Jumlah baris per batch impor}
        {--province=* : Kode provinsi tertentu untuk --source=osm (default semua)}';

    protected $description = 'Sinkronkan data sekolah dari CSV/Dapodik (upsert per NPSN) atau OpenStreetMap (upsert per osm_id)';

    public function handle(SchoolImporter $importer, OverpassClient $overpass, OsmSchoolImporter $osm): int
    {
        if ($this->option('source') === 'osm') {
            // respons Overpass per provinsi bisa besar; ini proses CLI
            ini_set('memory_limit', '1024M');
            set_time_limit(0);

            return $this->syncOsm($overpass, $osm);
        }

        try {
            $source = $this->makeSource();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $chunkSize = max(1, (int) $this->option('chunk'));
        $totals = ['created' => 0, 'updated' => 0, 'failed' => 0];
        $offset = 0;
        $buffer = [];

        $this->info("Sumber: {$source->name()}");

        try {
            foreach ($source->records() as $record) {
                $buffer[] = $record;
                if (count($buffer) >= $chunkSize) {
                    $this->flush($importer, $buffer, $offset, $totals);
                    $offset += count($buffer);
                    $buffer = [];
                }
            }
            if ($buffer !== []) {
                $this->flush($importer, $buffer, $offset, $totals);
                $offset += count($buffer);
            }
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->table(['Dibuat', 'Diperbarui', 'Gagal'], [[$totals['created'], $totals['updated'], $totals['failed']]]);

        return self::SUCCESS;
    }

    /** @param  array<int, array<string, mixed>>  $buffer  @param  array{created: int, updated: int, failed: int}  $totals */
    private function flush(SchoolImporter $importer, array $buffer, int $offset, array &$totals): void
    {
        $result = $importer->import($buffer, $offset);

        $totals['created'] += $result['created'];
        $totals['updated'] += $result['updated'];
        $totals['failed'] += $result['failed'];

        foreach (array_slice($result['errors'], 0, 10) as $error) {
            $this->warn("Baris {$error['row']} (NPSN ".($error['npsn'] ?? '-')."): {$error['message']}");
        }
    }

    /**
     * Sekolah dari OpenStreetMap per provinsi: bounding box diambil dari batas provinsi di regions.
     * Kotak provinsi yang berdekatan saling tumpang tindih, jadi sekolah yang sama bisa muncul dua kali;
     * upsert per osm_id membuat hasilnya tetap satu baris per sekolah.
     */
    private function syncOsm(OverpassClient $overpass, OsmSchoolImporter $osm): int
    {
        $only = array_map('strval', (array) $this->option('province'));

        $provinces = DB::table('regions')
            ->where('level', 'prov')
            ->whereNotNull('boundary_polygon')
            ->when($only !== [], fn ($q) => $q->whereIn('code', $only))
            ->orderBy('code')
            ->selectRaw('code, name,
                ST_YMin(ST_Envelope(boundary_polygon::geometry)) AS south,
                ST_XMin(ST_Envelope(boundary_polygon::geometry)) AS west,
                ST_YMax(ST_Envelope(boundary_polygon::geometry)) AS north,
                ST_XMax(ST_Envelope(boundary_polygon::geometry)) AS east')
            ->get();

        if ($provinces->isEmpty()) {
            $this->error('Tidak ada provinsi dengan batas di tabel regions. Jalankan: php artisan regions:sync-wilayah');

            return self::FAILURE;
        }

        $totals = ['created' => 0, 'updated' => 0, 'failed' => 0, 'skipped' => []];
        foreach ($provinces as $province) {
            $this->line("Provinsi {$province->code} {$province->name}");
            try {
                $elements = $overpass->schoolsInBbox($province->south, $province->west, $province->north, $province->east);
                $result = $osm->import($elements);
            } catch (RuntimeException $e) {
                $this->warn("  dilewati: {$e->getMessage()}");
                continue;
            }

            $this->line('  elemen: '.count($elements).', dibuat: '.$result['created'].', diperbarui: '.$result['updated']
                .', gagal: '.$result['failed']);
            $totals['created'] += $result['created'];
            $totals['updated'] += $result['updated'];
            $totals['failed'] += $result['failed'];
            foreach ($result['skipped'] as $reason => $count) {
                $totals['skipped'][$reason] = ($totals['skipped'][$reason] ?? 0) + $count;
            }

            sleep(5);
        }

        $this->newLine();
        $this->table(['Dibuat', 'Diperbarui', 'Gagal'], [[$totals['created'], $totals['updated'], $totals['failed']]]);
        foreach ($totals['skipped'] as $reason => $count) {
            $this->line("Dilewati ({$reason}): {$count}");
        }

        return self::SUCCESS;
    }

    private function makeSource(): SchoolSource
    {
        $mapper = new SchoolRecordMapper(config('dapodik.fields'));

        return match ($this->option('source')) {
            'csv' => new CsvSchoolSource(
                (string) ($this->option('path') ?: config('dapodik.csv.path')),
                $mapper,
                (string) config('dapodik.csv.delimiter', ','),
            ),
            'dapodik' => new DapodikWebserviceSource($mapper, config('dapodik.webservice')),
            default => throw new RuntimeException('Sumber tidak dikenal. Pilih --source=csv atau --source=dapodik.'),
        };
    }
}
