<?php

namespace Database\Seeders;

use App\Enums\RegistrationRole;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder tabel `users`: akun demo untuk setiap role/persona RBAC di PRD (PBI-13 & PBI-14).
 *
 * Akun demo yang disediakan:
 * 1. Admin Dinas       : admin@eduinfra.test   (role: admin_dinas)
 * 2. Pelapor (Guru/3T) : guru@eduinfra.test    (role: pelapor_guru)
 * 3. Auditor / Relawan : auditor@eduinfra.test / hendra@eduinfra.test (role: auditor_relawan)
 * 4. Mitra CSR         : csr@eduinfra.test / budi@eduinfra.test       (role: mitra_csr)
 * 5. Akun Pending      : pending@eduinfra.test (is_verified: false, untuk uji penolakan/approval)
 *
 * Kata sandi semua akun demo: "password".
 *
 * Dijalankan manual: php artisan db:seed --class=UserSeeder
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $regionId = Schema::hasTable('regions') ? DB::table('regions')->value('id') : null;

        $accounts = [
            // 1. Role: Admin Dinas
            [
                'name' => 'Administrator Dinas',
                'email' => 'admin@eduinfra.test',
                'identity_number' => '198001012005011001',
                'phone' => '081200000001',
                'role_enum' => UserRole::AdminDinas->value,
                'registration_role' => null,
                'organization' => 'Dinas Pendidikan Provinsi',
                'region_id' => $regionId,
                'is_verified' => true,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'approved_at' => now(),
            ],

            // 2. Role: Pelapor Guru / Sekolah 3T
            [
                'name' => 'Siti Rahmawati, S.Pd (Pelapor Guru)',
                'email' => 'guru@eduinfra.test',
                'identity_number' => '198803122010012003',
                'phone' => '081200000002',
                'role_enum' => UserRole::PelaporGuru->value,
                'registration_role' => RegistrationRole::SatdikSekolah3t->value,
                'organization' => 'SDN 01 Sambas (Koridor 3T)',
                'region_id' => $regionId,
                'is_verified' => true,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'approved_at' => now(),
            ],

            // 3. Role: Auditor / Relawan
            [
                'name' => 'Auditor Lapangan 3T',
                'email' => 'auditor@eduinfra.test',
                'identity_number' => '197502142001121001',
                'phone' => '081200000003',
                'role_enum' => UserRole::AuditorRelawan->value,
                'registration_role' => RegistrationRole::Verifikator3t->value,
                'organization' => 'Balai Prasarana & Relawan Koridor 3T',
                'region_id' => $regionId,
                'is_verified' => true,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'approved_at' => now(),
            ],
            [
                'name' => 'Ir. Hendra',
                'email' => 'hendra@eduinfra.test',
                'identity_number' => '197805122003121004',
                'phone' => '081200000005',
                'role_enum' => UserRole::AuditorRelawan->value,
                'registration_role' => RegistrationRole::Verifikator3t->value,
                'organization' => 'Balai Prasarana Permukiman Wilayah',
                'region_id' => $regionId,
                'is_verified' => true,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'approved_at' => now(),
            ],

            // 4. Role: Mitra CSR / Rekanan
            [
                'name' => 'Mitra CSR Indonesia',
                'email' => 'csr@eduinfra.test',
                'identity_number' => '3171012304850001',
                'phone' => '081200000004',
                'role_enum' => UserRole::MitraCsr->value,
                'registration_role' => RegistrationRole::KonsultanRekanan->value,
                'organization' => 'Yayasan Peduli Sarpras 3T (Mitra CSR)',
                'region_id' => $regionId,
                'is_verified' => true,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'approved_at' => now(),
            ],
            [
                'name' => 'Dr. Budi Santoso, M.Eng',
                'email' => 'budi@eduinfra.test',
                'identity_number' => '198403152008121002',
                'phone' => '081200000006',
                'role_enum' => UserRole::MitraCsr->value,
                'registration_role' => RegistrationRole::KonsultanRekanan->value,
                'organization' => 'PT Wahana Cipta Bangsa (Mitra CSR)',
                'region_id' => $regionId,
                'is_verified' => true,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'approved_at' => now(),
            ],

            // 5. Akun Pending (Belum Verifikasi)
            [
                'name' => 'Akun Belum Diverifikasi',
                'email' => 'pending@eduinfra.test',
                'identity_number' => '198504202010011003',
                'phone' => '081200000007',
                'role_enum' => UserRole::PelaporGuru->value,
                'registration_role' => RegistrationRole::SatdikSekolah3t->value,
                'organization' => 'SMPN 03 Kepulauan Aru',
                'region_id' => $regionId,
                'is_verified' => false,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'approved_at' => null,
            ],
        ];

        User::unguarded(function () use ($accounts): void {
            foreach ($accounts as $account) {
                User::query()->updateOrCreate(
                    ['email' => $account['email']],
                    array_merge($account, [
                        'password' => 'password',
                    ]),
                );
            }
        });
    }
}
