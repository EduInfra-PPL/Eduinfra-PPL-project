<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Taksonomi fasilitas awal (PBI-03). Sama dengan bagian facility_categories di
 * database/sql/02_seed.sql, tetapi tanpa data dummy sekolah, sehingga aman
 * dijalankan pada database yang sudah berisi sekolah hasil impor.
 *
 *   php artisan db:seed --class=FacilityCategorySeeder
 *
 * Idempoten: kategori yang sudah ada (nama + sub-kategori) tidak diubah,
 * sehingga bobot urgensi yang sudah diatur Admin tidak tertimpa.
 */
class FacilityCategorySeeder extends Seeder
{
    /** @var array<int, array{0: string, 1: string, 2: int, 3: string}> */
    private const CATEGORIES = [
        ['Ruang Kelas', 'Struktur dan Atap', 5, 'ruang'],
        ['Ruang Kelas', 'Lantai dan Dinding', 4, 'ruang'],
        ['Ruang Kelas', 'Plafon', 3, 'ruang'],
        ['Ruang Kelas', 'Mebel', 2, 'set'],
        ['Sanitasi', 'Toilet Siswa', 4, 'unit'],
        ['Sanitasi', 'Air Minum', 5, 'titik'],
        ['Sanitasi', 'Fasilitas Cuci Tangan', 3, 'unit'],
        ['Laboratorium', 'Laboratorium IPA', 3, 'ruang'],
        ['Laboratorium', 'Laboratorium Komputer', 3, 'ruang'],
        ['Perpustakaan', 'Ruang dan Rak Buku', 2, 'ruang'],
        ['Kelistrikan', 'Instalasi Listrik', 4, 'paket'],
    ];

    public function run(): void
    {
        $rows = array_map(fn (array $c) => [
            'name' => $c[0],
            'sub_category' => $c[1],
            'urgency_weight' => $c[2],
            'default_unit' => $c[3],
        ], self::CATEGORIES);

        DB::table('facility_categories')->insertOrIgnore($rows);
    }
}
