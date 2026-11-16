<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
| Menambahkan sumber 'manual' (sekolah yang didaftarkan admin lewat halaman).
| SQL-nya ada di database/sql/01d_schools_manual_source.sql.
*/
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/01d_schools_manual_source.sql')));
    }

    public function down(): void
    {
        DB::unprepared("UPDATE schools SET source = 'dapodik' WHERE source = 'manual';
            ALTER TABLE schools DROP CONSTRAINT IF EXISTS schools_source_chk;
            ALTER TABLE schools ADD CONSTRAINT schools_source_chk CHECK (source IN ('dapodik', 'osm'));");
    }
};
