<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
| Sekolah dari OpenStreetMap belum memiliki NPSN. SQL-nya ada di
| database/sql/01c_schools_osm_source.sql agar identik dengan docker-compose.
| Down tidak dibuat: menghapus kolom akan menghilangkan identitas sekolah OSM.
*/
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/01c_schools_osm_source.sql')));
    }

    public function down(): void
    {
        // sengaja kosong: lihat komentar di atas
    }
};
