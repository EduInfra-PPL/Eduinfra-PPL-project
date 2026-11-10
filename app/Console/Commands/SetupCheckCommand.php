<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Memeriksa prasyarat sebelum proyek dijalankan di komputer baru. Tidak mengubah data.
 */
class SetupCheckCommand extends Command
{
    protected $signature = 'setup:check';

    protected $description = 'Periksa prasyarat instalasi (PHP, PostgreSQL + PostGIS, migrasi, data, key, dan HTTPS)';

    public function handle(): int
    {
        $rows = [];
        $fails = 0;

        $add = function (string $check, string $status, string $hint = '') use (&$rows, &$fails): void {
            $rows[] = [$check, $status, $hint];
            if ($status === 'GAGAL') {
                $fails++;
            }
        };

        // 1. PHP dan ekstensi
        foreach (['pdo_pgsql', 'curl', 'openssl', 'mbstring', 'json'] as $ext) {
            extension_loaded($ext)
                ? $add("Ekstensi PHP {$ext}", 'OK')
                : $add("Ekstensi PHP {$ext}", 'GAGAL', "Aktifkan extension={$ext} di php.ini");
        }

        // 2. Database dan PostGIS
        try {
            DB::select('select 1');
            $add('Koneksi database', 'OK', config('database.connections.pgsql.database'));
        } catch (Throwable $e) {
            $add('Koneksi database', 'GAGAL', 'Nyalakan PostgreSQL/Docker dan cek DB_* di .env');

            return $this->report($rows, $fails);
        }

        $postgis = DB::selectOne("select extversion as v from pg_extension where extname = 'postgis'");
        $postgis
            ? $add('Ekstensi PostGIS', 'OK', 'versi '.$postgis->v)
            : $add('Ekstensi PostGIS', 'GAGAL', 'Pakai image postgis (docker compose), bukan postgres biasa');

        // 3. Migrasi
        $tables = ['regions', 'schools', 'facility_categories', 'users'];
        $missing = array_values(array_filter($tables, fn (string $t): bool => ! DB::getSchemaBuilder()->hasTable($t)));
        $missing === []
            ? $add('Tabel utama (migrasi)', 'OK')
            : $add('Tabel utama (migrasi)', 'GAGAL', 'Jalankan: php artisan migrate (kurang: '.implode(', ', $missing).')');

        $columns = DB::getSchemaBuilder()->hasTable('regions')
            ? DB::getSchemaBuilder()->getColumnListing('regions') : [];
        in_array('is_3t', $columns, true)
            ? $add('Kolom kategori 3T', 'OK')
            : $add('Kolom kategori 3T', 'GAGAL', 'Jalankan: php artisan migrate');

        // 4. Data
        if (DB::getSchemaBuilder()->hasTable('regions')) {
            $regions = (int) DB::table('regions')->count();
            $regions > 0
                ? $add('Wilayah (regions)', 'OK', "{$regions} wilayah")
                : $add('Wilayah (regions)', 'PERINGATAN', 'Jalankan: php artisan db:seed --class=Domain1Seeder lalu php artisan regions:sync-wilayah');
        }
        if (DB::getSchemaBuilder()->hasTable('schools')) {
            $schools = (int) DB::table('schools')->count();
            $schools > 0
                ? $add('Sekolah (schools)', 'OK', "{$schools} sekolah")
                : $add('Sekolah (schools)', 'PERINGATAN', 'Belum ada data sekolah. Lihat README bagian sinkronisasi');
        }

        // 5. Konfigurasi
        filled(config('app.key'))
            ? $add('APP_KEY', 'OK')
            : $add('APP_KEY', 'GAGAL', 'Jalankan: php artisan key:generate');

        filled(config('services.apiindonesia.key'))
            ? $add('APIINDONESIA_KEY', 'OK')
            : $add('APIINDONESIA_KEY', 'PERINGATAN', 'Isi di .env bila ingin memakai sinkronisasi API Indonesia');

        is_writable(storage_path())
            ? $add('Folder storage', 'OK')
            : $add('Folder storage', 'GAGAL', 'Berikan izin tulis pada folder storage');

        // 6. Aset dan HTTPS keluar
        is_file(public_path('build/manifest.json'))
            ? $add('Aset frontend (npm run build)', 'OK')
            : $add('Aset frontend (npm run build)', 'PERINGATAN', 'Jalankan: npm run build');

        try {
            Http::timeout(15)
                ->withOptions(['verify' => config('eduinfra.ca_bundle', true)])
                ->get(config('services.apiindonesia.base_url'));
            $add('HTTPS keluar (sertifikat)', 'OK');
        } catch (Throwable $e) {
            $add('HTTPS keluar (sertifikat)', 'GAGAL', 'Isi EDUINFRA_CA_BUNDLE di .env dengan path ca-bundle.crt');
        }

        return $this->report($rows, $fails);
    }

    /** @param  list<array{0: string, 1: string, 2: string}>  $rows */
    private function report(array $rows, int $fails): int
    {
        $this->table(['Pemeriksaan', 'Status', 'Keterangan'], $rows);

        if ($fails === 0) {
            $this->info('Semua pemeriksaan wajib lulus.');

            return self::SUCCESS;
        }

        $this->error("{$fails} pemeriksaan wajib gagal. Perbaiki sesuai keterangan lalu jalankan ulang.");

        return self::FAILURE;
    }
}
