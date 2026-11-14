<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder tabel `users`: akun demo untuk mencoba login (PBI-13).
 * Kata sandi semua akun: "password". Satu akun sengaja belum terverifikasi
 * (is_verified = false) untuk memperlihatkan penolakan login.
 *
 * Dijalankan manual:  php artisan db:seed --class=UserSeeder
 * Tidak berjalan di production.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $accounts = [
            ['Administrator Sistem', 'admin@eduinfra.test', '198001012005011001', true],
            ['Ir. Hendra', 'hendra@eduinfra.test', '197805122003121004', true],
            ['Dr. Budi Santoso, M.Eng', 'budi@eduinfra.test', '198403152008121002', true],
            ['Akun Belum Diverifikasi', 'pending@eduinfra.test', '198504202010011003', false],
        ];

        // unguarded: model User memakai atribut #[Fillable] yang belum dikenali Laravel 11.
        User::unguarded(function () use ($accounts): void {
            foreach ($accounts as [$name, $email, $identity, $verified]) {
                User::query()->updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'identity_number' => $identity,
                        'password' => 'password',
                        'is_verified' => $verified,
                        'email_verified_at' => now(),
                    ],
                );
            }
        });
    }
}
