<?php

namespace Database\Seeders;

use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\Region;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DamageReportSeeder extends Seeder
{
    public function run(): void
    {
        $this->normalizeInitialMasterSchools();

        $categories = FacilityCategory::where('is_enabled', true)->get();
        if ($categories->isEmpty()) {
            return;
        }

        $findCategory = function (string $name, string $sub = '') use ($categories): int {
            $match = $categories->first(fn ($c) => $c->name === $name && ($sub === '' || $c->sub_category === $sub))
                ?? $categories->first(fn ($c) => $c->name === $name)
                ?? $categories->first();

            return (int) $match->id;
        };

        $findSchoolByRegionOrFallback = function (string $regionCode, string $preferredLevel = 'SD', int $offset = 0): ?School {
            $region = Region::where('code', $regionCode)->first();
            if ($region) {
                $school = School::active()
                    ->where('region_id', $region->id)
                    ->where('name', 'not like', '%Dummy%')
                    ->when($preferredLevel, fn ($q) => $q->where('educational_level', $preferredLevel))
                    ->orderBy('id')
                    ->skip($offset)
                    ->first()
                    ?? School::active()
                        ->where('region_id', $region->id)
                        ->orderBy('id')
                        ->skip($offset)
                        ->first();

                if ($school) {
                    return $school;
                }
            }

            return School::active()
                ->where('name', 'not like', '%Dummy%')
                ->orderBy('id')
                ->skip($offset)
                ->first()
                ?? School::active()->orderBy('id')->skip($offset)->first();
        };

        $sAlorSd = $findSchoolByRegionOrFallback('53.05', 'SD', 0);
        $sAlorSmp = $findSchoolByRegionOrFallback('53.05', 'SMP', 0);
        $sAsmatSmp = $findSchoolByRegionOrFallback('93.04', 'SMP', 0);
        $sPegBintangSma = $findSchoolByRegionOrFallback('95.02', 'SMA', 0);
        $sTtsSd = $findSchoolByRegionOrFallback('53.02', 'SD', 0);
        $sNdugaSd = $findSchoolByRegionOrFallback('95.08', 'SD', 0);
        $sJayawijayaSmp = $findSchoolByRegionOrFallback('95.01', 'SMP', 0);
        $sKupangSma = $findSchoolByRegionOrFallback('53.71', 'SMA', 0);

        $reportsData = [
            [
                'ticket_num' => 'TKT-2026-0798',
                'school_id' => $sAlorSd?->id,
                'category_id' => $findCategory('Ruang Kelas', 'Atap'),
                'title' => 'Rangka Atap 4 Ruang Kelas & Sanitasi Sekolah Rusak Berat',
                'description' => 'Kuda-kuda kayu lapuk dan penutup atap seng berkarat berat di 4 ruang kelas utama, disertai kerusakan instalasi sanitasi air bersih.',
                'severity_level' => 5,
                'status' => 'in_repair',
            ],
            [
                'ticket_num' => 'TKT-2026-0812',
                'school_id' => $sAlorSmp?->id,
                'category_id' => $findCategory('Ruang Kelas', 'Struktur'),
                'title' => 'Struktur Kolom Ruang Kelas Retak & Tanggul Drainase Tergerus Abrasi',
                'description' => 'Pondasi dan balok sloof ruang belajar mengalami penurunan akibat abrasi pesisir kepulauan sehingga memerlukan perkuatan struktur.',
                'severity_level' => 4,
                'status' => 'in_repair',
            ],
            [
                'ticket_num' => 'TKT-2026-0840',
                'school_id' => $sAsmatSmp?->id,
                'category_id' => $findCategory('Sanitasi', 'Air Bersih'),
                'title' => 'Kerusakan Selasar Panggung Kayu Besi & Instalasi Filtrasi Air Bersih',
                'description' => 'Tiang pancang selasar kayu ulin patah terendam pasang surut rawa dan tandon air bersih sekolah bocor.',
                'severity_level' => 4,
                'status' => 'in_repair',
            ],
            [
                'ticket_num' => 'TKT-2026-0775',
                'school_id' => $sPegBintangSma?->id,
                'category_id' => $findCategory('Laboratorium', 'Komputer'),
                'title' => 'Rehabilitasi Ruang Laboratorium & Panel Kelistrikan Tenaga Surya',
                'description' => 'Atap ruang laboratorium bocor mengenai perangkat praktik siswa dan baterai cadangan kelistrikan sekolah tidak berfungsi.',
                'severity_level' => 4,
                'status' => 'in_repair',
            ],
            [
                'ticket_num' => 'TKT-2026-0750',
                'school_id' => $sTtsSd?->id,
                'category_id' => $findCategory('Ruang Kelas', 'Lantai dan Dinding'),
                'title' => 'Perbaikan Penutup Atap Korosi & Penguatan Dinding Bata Ruang Kelas',
                'description' => 'Dinding pasangan bata retak memanjang dan lantai keramik ruang kelas terangkat di 3 ruang belajar.',
                'severity_level' => 4,
                'status' => 'in_repair',
            ],
            [
                'ticket_num' => 'TKT-2026-0761',
                'school_id' => $sNdugaSd?->id,
                'category_id' => $findCategory('Sanitasi', 'Toilet Siswa Putri'),
                'title' => 'Pembangunan Bilik Sanitasi Terpisah & Perbaikan Ruang Baca',
                'description' => 'Bilik sanitasi siswa putri rusak berat tanpa akses air mengalir serta plafon perpustakaan mengalami kebocoran.',
                'severity_level' => 5,
                'status' => 'in_repair',
            ],
            [
                'ticket_num' => 'TKT-2026-0891',
                'school_id' => $sJayawijayaSmp?->id,
                'category_id' => $findCategory('Laboratorium', 'IPA'),
                'title' => 'Atap Ruang Laboratorium IPA Ambruk & Meja Praktikum Rusak',
                'description' => 'Curah hujan tinggi menyebabkan plafon dan kuda-kuda ruang laboratorium IPA ambruk, telah diverifikasi pengawas dinas.',
                'severity_level' => 5,
                'status' => 'verified',
            ],
            [
                'ticket_num' => 'TKT-2026-0895',
                'school_id' => $sKupangSma?->id,
                'category_id' => $findCategory('Utilitas', 'Listrik'),
                'title' => 'Kerusakan Jaringan Panel Listrik Utama & Sanitasi Siswa Putra',
                'description' => 'Korsleting pada panel distribusi utama ruang kelas dan kerusakan pipa distribusi air toilet siswa putra.',
                'severity_level' => 3,
                'status' => 'verified',
            ],
        ];

        foreach ($reportsData as $rep) {
            if (! $rep['school_id']) {
                continue;
            }

            DamageReport::updateOrCreate(
                ['ticket_num' => $rep['ticket_num']],
                $rep
            );
        }
    }

    /**
     * Memperbarui nama sekolah bawaan 02_seed.sql yang masih berlabel "Dummy"
     * menjadi identitas satuan pendidikan riil sesuai wilayah Master Data.
     */
    private function normalizeInitialMasterSchools(): void
    {
        $replacements = [
            '99000001' => ['npsn' => '50305811', 'name' => 'SD Inpres Kalabahi Timur 01', 'address' => 'Jl. Diponegoro No. 14, Teluk Mutiara, Kabupaten Alor'],
            '99000002' => ['npsn' => '50305842', 'name' => 'SMP Negeri Satu Atap Pantar Barat', 'address' => 'Kec. Pantar Barat, Kabupaten Alor'],
            '99000003' => ['npsn' => '50305890', 'name' => 'SMA Negeri 1 Kabola Alor', 'address' => 'Jl. Pantai Kabola, Kabupaten Alor'],
            '99000004' => ['npsn' => '50300712', 'name' => 'SD Inpres Oebelo Amanuban', 'address' => 'Kec. Amanuban Selatan, Kabupaten Timor Tengah Selatan'],
            '99000005' => ['npsn' => '50300745', 'name' => 'SMP Negeri 2 Mollo Utara', 'address' => 'Kec. Mollo Utara, Kabupaten Timor Tengah Selatan'],
            '99000006' => ['npsn' => '50300788', 'name' => 'SMA Negeri 1 Soe', 'address' => 'Jl. Kartini No. 8, Kota Soe, Kabupaten Timor Tengah Selatan'],
            '99000007' => ['npsn' => '50304910', 'name' => 'SD Negeri Oeba 3 Kota Kupang', 'address' => 'Kel. Fatubesi, Kec. Kota Lama, Kota Kupang'],
            '99000008' => ['npsn' => '50304952', 'name' => 'SMP Negeri 5 Kota Kupang', 'address' => 'Jl. Frans Seda, Kelapa Lima, Kota Kupang'],
            '99000009' => ['npsn' => '50304981', 'name' => 'SMA Negeri 3 Kota Kupang', 'address' => 'Jl. W.J. Lalamentik, Oebufu, Kota Kupang'],
            '99000010' => ['npsn' => '60302801', 'name' => 'SD YPPK Salib Suci Agats', 'address' => 'Distrik Agats, Kabupaten Asmat'],
            '99000011' => ['npsn' => '60302911', 'name' => 'SMP Negeri Satu Atap 01 Agats', 'address' => 'Jl. Pemuda Bis Agats, Distrik Agats, Kabupaten Asmat'],
            '99000012' => ['npsn' => '60302944', 'name' => 'SMA Negeri 1 Agats', 'address' => 'Distrik Agats, Kabupaten Asmat'],
            '99000013' => ['npsn' => '60302104', 'name' => 'SD Inpres Oksibil Perbatasan', 'address' => 'Distrik Oksibil, Kabupaten Pegunungan Bintang'],
            '99000014' => ['npsn' => '60302138', 'name' => 'SMP Negeri 1 Batom', 'address' => 'Distrik Batom, Kabupaten Pegunungan Bintang'],
            '99000015' => ['npsn' => '60302175', 'name' => 'SMA Negeri 1 Oksibil', 'address' => 'Jl. Bandara Oksibil, Kabupaten Pegunungan Bintang'],
            '99000016' => ['npsn' => '60303410', 'name' => 'SD Negeri Inpres Kenyam', 'address' => 'Distrik Kenyam, Kabupaten Nduga'],
            '99000017' => ['npsn' => '60303442', 'name' => 'SMP Negeri 1 Kenyam', 'address' => 'Distrik Kenyam, Kabupaten Nduga'],
            '99000018' => ['npsn' => '60303479', 'name' => 'SMA Negeri 1 Kenyam Nduga', 'address' => 'Distrik Kenyam, Kabupaten Nduga'],
            '99000019' => ['npsn' => '60301502', 'name' => 'SD Negeri Inpres Wamena', 'address' => 'Distrik Wamena, Kabupaten Jayawijaya'],
            '99000020' => ['npsn' => '60301548', 'name' => 'SMP Negeri 2 Wamena', 'address' => 'Jl. Yos Sudarso Wamena, Kabupaten Jayawijaya'],
            '99000021' => ['npsn' => '60301589', 'name' => 'SMA Negeri 1 Wamena', 'address' => 'Jl. Trikora Wamena, Kabupaten Jayawijaya'],
            '99000022' => ['npsn' => '20205411', 'name' => 'SD Negeri Ciwidey 02', 'address' => 'Kec. Ciwidey, Kabupaten Bandung'],
            '99000023' => ['npsn' => '20205488', 'name' => 'SMP Negeri 1 Pangalengan', 'address' => 'Kec. Pangalengan, Kabupaten Bandung'],
            '99000024' => ['npsn' => '20205512', 'name' => 'SMA Negeri 1 Soreang', 'address' => 'Jl. Raya Soreang-Banjaran, Kabupaten Bandung'],
            '99000025' => ['npsn' => '20245102', 'name' => 'SD Negeri Banjarsari Kota Bandung', 'address' => 'Jl. Merdeka No. 22, Sumur Bandung, Kota Bandung'],
            '99000026' => ['npsn' => '20245180', 'name' => 'SMP Negeri 2 Kota Bandung', 'address' => 'Jl. Sumatera No. 42, Kota Bandung'],
            '99000027' => ['npsn' => '20245215', 'name' => 'SMA Negeri 3 Kota Bandung', 'address' => 'Jl. Belitung No. 8, Kota Bandung'],
        ];

        foreach ($replacements as $oldNpsn => $attrs) {
            $newNpsnExists = DB::table('schools')->where('npsn', $attrs['npsn'])->exists();
            $updatePayload = [
                'name' => $attrs['name'],
                'address' => $attrs['address'],
            ];
            if (! $newNpsnExists) {
                $updatePayload['npsn'] = $attrs['npsn'];
            }

            DB::table('schools')
                ->where('npsn', $oldNpsn)
                ->update($updatePayload);
        }
    }
}
