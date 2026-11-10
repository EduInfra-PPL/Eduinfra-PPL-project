<?php

namespace App\Console\Commands;

use App\Services\Wilayah\RegionBoundaryImporter;
use App\Services\Wilayah\WilayahIdClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Mengisi provinsi dan kabupaten/kota (batas BIG via wilayah-id) ke tabel regions.
 * Setelah batas dimuat, kabupaten daerah tertinggal dari database/sql/02_seed.sql diberi tanda 3T.
 */
class SyncRegionsWilayahCommand extends Command
{
    protected $signature = 'regions:sync-wilayah
        {--province=* : Kode provinsi tertentu (mis. 32); default semua provinsi}
        {--skip-3t : Lewati penandaan kabupaten tertinggal dari seed}';

    protected $description = 'Impor provinsi dan kabupaten/kota beserta batas dari wilayah-id (sumber BIG)';

    public function handle(WilayahIdClient $client, RegionBoundaryImporter $importer): int
    {
        // respons GeoJSON per provinsi bisa besar; sinkronisasi ini berjalan sebagai proses CLI
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $provinces = $this->provinces($client);
            $only = array_map('strval', (array) $this->option('province'));

            $created = 0;
            $updated = 0;
            foreach ($provinces as $province) {
                if ($only !== [] && ! in_array($province['code'], $only, true)) {
                    continue;
                }

                $this->line("Provinsi {$province['code']} {$province['name']}");
                $result = $importer->upsert($province, $province['geometry']);
                $result ? $created++ : $updated++;

                $collection = $client->get('boundaries/regencies', [
                    'province_code' => $province['code'],
                    'geometry' => 'true',
                    'limit' => 200,
                ]);

                foreach ($collection['features'] ?? [] as $feature) {
                    $props = $feature['properties'] ?? [];
                    $region = [
                        'code' => RegionBoundaryImporter::formatKabCode((string) ($props['kode_kab'] ?? '')),
                        'name' => (string) ($props['nama_kabupaten'] ?? ''),
                        'level' => 'kab',
                    ];
                    if ($region['code'] === '' || $region['name'] === '' || empty($feature['geometry'])) {
                        continue;
                    }
                    $importer->upsert($region, $feature['geometry']) ? $created++ : $updated++;
                }
            }
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Wilayah: {$created} baru, {$updated} diperbarui.");

        if (! $this->option('skip-3t') && $only === []) {
            $this->markTertinggal();
        }

        return self::SUCCESS;
    }

    /** @return list<array{code: string, name: string, level: string, geometry: array<string, mixed>}> */
    private function provinces(WilayahIdClient $client): array
    {
        $collection = $client->get('boundaries/provinces', ['geometry' => 'true']);

        $provinces = [];
        foreach ($collection['features'] ?? [] as $feature) {
            $props = $feature['properties'] ?? [];
            $code = preg_replace('/\D/', '', (string) ($props['kode_prov'] ?? '')) ?? '';
            if ($code === '' || empty($feature['geometry'])) {
                continue;
            }
            $provinces[] = [
                'code' => $code,
                'name' => (string) ($props['nama_provinsi'] ?? ''),
                'level' => 'prov',
                'geometry' => $feature['geometry'],
            ];
        }

        if ($provinces === []) {
            throw new RuntimeException('Respons batas provinsi kosong dari wilayah-id.');
        }

        return $provinces;
    }

    /**
     * Kabupaten daerah tertinggal (Tier-1, Perpres 63/2020) dari daftar di 02_seed.sql.
     */
    private function markTertinggal(): void
    {
        $seed = file_get_contents(database_path('sql/02_seed.sql'));
        preg_match_all("/\('(\d{2}\.\d{2})', '[^']*', 'kab', 1\)/", $seed, $matches);
        $codes = array_values(array_unique($matches[1]));

        $matched = DB::table('regions')->whereIn('code', $codes)->where('level', 'kab')
            ->update(['is_3t_tier' => 1, 'is_tertinggal' => true]);

        $this->info('Kabupaten tertinggal ditandai: '.$matched.' dari '.count($codes).' kode di seed.');
    }
}
