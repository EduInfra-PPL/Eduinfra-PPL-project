<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
| Kategori 3T (is_tertinggal, is_terdepan, is_terluar) dan kolom turunan is_3t
| pada tabel regions. SQL-nya ada di database/sql/01b_regions_3t_categories.sql
| agar identik dengan yang dijalankan docker-compose. Aman untuk basis data yang sudah ada.
*/
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/01b_regions_3t_categories.sql')));
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DROP INDEX IF EXISTS idx_regions_is_3t;
            ALTER TABLE regions
                DROP COLUMN IF EXISTS is_3t,
                DROP COLUMN IF EXISTS is_terluar,
                DROP COLUMN IF EXISTS is_terdepan,
                DROP COLUMN IF EXISTS is_tertinggal;
            SQL);
    }
};
