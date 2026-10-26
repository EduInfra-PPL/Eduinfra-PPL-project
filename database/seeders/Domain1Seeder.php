<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Mengisi data dummy Domain 1 dari database/sql/02_seed.sql (idempoten).
 * Opsional data uji kinerja 50.000 sekolah: SEED_PERF=1 php artisan db:seed --class=Domain1Seeder
 */
class Domain1Seeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/02_seed.sql')));

        if (env('SEED_PERF')) {
            DB::unprepared(file_get_contents(database_path('sql/optional/03_seed_perf_50k.sql')));
        }
    }
}
