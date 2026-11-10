<?php

namespace App\Enums;

/**
 * 4 kartu "Peran & Instansi Penugasan" pada form registrasi.
 * Setiap kartu dipetakan ke satu persona RBAC (UserRole) lewat persona().
 * Admin Dinas tidak ada di sini: akun admin hanya dibuat oleh administrator.
 */
enum RegistrationRole: string
{
    case Verifikator3t = 'verifikator_3t';
    case SatdikSekolah3t = 'satdik_sekolah_3t';
    case AuditorPengawas = 'auditor_pengawas';
    case KonsultanRekanan = 'konsultan_rekanan';

    public function title(): string
    {
        return match ($this) {
            self::Verifikator3t => 'Verifikator 3T',
            self::SatdikSekolah3t => 'Satdik Sekolah 3T',
            self::AuditorPengawas => 'Auditor Pengawas',
            self::KonsultanRekanan => 'Konsultan / Rekanan',
        };
    }

    public function hint(): string
    {
        return match ($this) {
            self::Verifikator3t => 'Tim Lapangan & Balai Cipta Karya',
            self::SatdikSekolah3t => 'Kepala Sekolah / Komite Wilayah Terluar',
            self::AuditorPengawas => 'Kemendikbud / BPKP / Inspektorat',
            self::KonsultanRekanan => 'Badan Usaha LPJK Terakreditasi',
        };
    }

    /**
     * Pemetaan kartu pendaftaran -> persona RBAC.
     * Konsultan / Rekanan dipetakan ke mitra_csr (hak paling terbatas) sampai PM menetapkan persona vendor.
     */
    public function persona(): UserRole
    {
        return match ($this) {
            self::Verifikator3t => UserRole::AuditorRelawan,
            self::SatdikSekolah3t => UserRole::PelaporGuru,
            self::AuditorPengawas => UserRole::AuditorRelawan,
            self::KonsultanRekanan => UserRole::MitraCsr,
        };
    }
}