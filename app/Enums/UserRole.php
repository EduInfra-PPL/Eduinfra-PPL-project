<?php

namespace App\Enums;

/**
 * 4 persona RBAC pada ERD (users.role_enum). Sumber: AGENT_RULE.md bagian 5, Domain 5.
 */
enum UserRole: string
{
    case PelaporGuru = 'pelapor_guru';
    case AuditorRelawan = 'auditor_relawan';
    case MitraCsr = 'mitra_csr';
    case AdminDinas = 'admin_dinas';

    public function label(): string
    {
        return match ($this) {
            self::PelaporGuru => 'Pelapor (Guru / Sekolah)',
            self::AuditorRelawan => 'Auditor / Relawan',
            self::MitraCsr => 'Mitra CSR',
            self::AdminDinas => 'Admin Dinas',
        };
    }
}