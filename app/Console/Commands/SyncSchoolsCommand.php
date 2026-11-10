<?php

namespace App\Console\Commands;

use App\Services\Dapodik\CsvSchoolSource;
use App\Services\Dapodik\DapodikWebserviceSource;
use App\Services\Dapodik\SchoolRecordMapper;
use App\Services\Dapodik\SchoolSource;
use App\Services\Schools\SchoolImporter;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Sinkronisasi data sekolah dari sumber eksternal (CSV ekspor Dapodik atau webservice Dapodik).
 * Upsert berdasarkan NPSN lewat SchoolImporter yang sama dengan endpoint impor API.
 */
class SyncSchoolsCommand extends Command
{
    protected $signature = 'schools:sync
        {--source=csv : Sumber data: csv atau dapodik}
        {--path= : Lokasi berkas CSV (menimpa DAPODIK_CSV_PATH)}
        {--chunk=500 : Jumlah baris per batch impor}';

    protected $description = 'Sinkronkan data sekolah dari berkas CSV atau webservice Dapodik (upsert per NPSN)';

    public function handle(SchoolImporter $importer): int
    {
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
