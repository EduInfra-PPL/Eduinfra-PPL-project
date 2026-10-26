<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
| Domain 1: regions, facility_categories, schools.
| Skema dipegang satu file SQL idempoten (database/sql/01_schema.sql) supaya
| identik dengan yang dijalankan docker-compose dan memakai tipe PostGIS
| geography + indeks GiST apa adanya. Migration ini hanya menjalankannya.
*/
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/01_schema.sql')));
    }

    public function down(): void
    {
        // Fungsi set_updated_at() dan ekstensi postgis sengaja tidak dihapus
        // karena dipakai bersama oleh domain lain.
        DB::unprepared('DROP TABLE IF EXISTS schools, facility_categories, regions CASCADE');
    }
};
