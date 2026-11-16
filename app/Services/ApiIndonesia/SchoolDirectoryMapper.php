<?php

namespace App\Services\ApiIndonesia;

/**
 * Mengubah satu baris dari API Indonesia (GET /api/v1/sekolah) menjadi baris sekolah internal.
 * Fungsi murni, tanpa akses jaringan atau database.
 *
 * Contoh respons: npsn, name, jenis (SD/SMP/SMA/SMK/SLB), status (Negeri/Swasta),
 * regency_id ("3273"), address, lat, lng, phone, email, accreditation.
 */
class SchoolDirectoryMapper
{
    /**
     * @param  array<string, mixed>  $row
     * @return array{record: array<string, mixed>|null, reason: string|null}
     */
    public function toRecord(array $row): array
    {
        $npsn = trim((string) ($row['npsn'] ?? ''));
        if (! preg_match('/^[0-9]{8}$/', $npsn)) {
            return ['record' => null, 'reason' => 'npsn_tidak_valid'];
        }

        $level = $this->level((string) ($row['jenis'] ?? ''));
        if ($level === null) {
            // SLB mencakup beberapa jenjang sekaligus, sehingga tidak dimasukkan ke SD/SMP/SMA
            return ['record' => null, 'reason' => 'jenjang_tidak_didukung'];
        }

        $regionCode = $this->regionCode((string) ($row['regency_id'] ?? ''));
        if ($regionCode === null) {
            return ['record' => null, 'reason' => 'kode_wilayah_tidak_valid'];
        }

        return [
            'record' => [
                'npsn' => $npsn,
                'name' => mb_substr(trim((string) ($row['name'] ?? '')), 0, 200),
                'educational_level' => $level,
                'address' => $this->nullableString($row['address'] ?? null, 1000),
                'lat' => $this->coordinate($row['lat'] ?? null),
                'lng' => $this->coordinate($row['lng'] ?? null),
                'region_code' => $regionCode,
                'accreditation' => $this->accreditation($row['accreditation'] ?? null),
                'contact_phone' => $this->nullableString($row['phone'] ?? null, 30),
                'contact_email' => $this->email($row['email'] ?? null),
            ],
            'reason' => null,
        ];
    }

    /** SD, SMP, SMA, dan SMK (dipetakan ke SMA). SLB dan jenjang lain mengembalikan null. */
    public function level(string $jenis): ?string
    {
        return match (strtoupper(trim($jenis))) {
            'SD' => 'SD',
            'SMP' => 'SMP',
            'SMA', 'SMK' => 'SMA',
            default => null,
        };
    }

    /** "3273" menjadi "32.73", sesuai format regions.code. */
    public function regionCode(string $regencyId): ?string
    {
        $digits = preg_replace('/\D/', '', $regencyId) ?? '';

        return strlen($digits) === 4 ? substr($digits, 0, 2).'.'.substr($digits, 2) : null;
    }

    private function accreditation(mixed $value): ?string
    {
        $grade = strtoupper(trim((string) $value));

        return in_array($grade, ['A', 'B', 'C'], true) ? $grade : null;
    }

    private function coordinate(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function email(mixed $value): ?string
    {
        $email = trim((string) $value);

        return ($email !== '' && mb_strlen($email) <= 150 && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : null;
    }

    private function nullableString(mixed $value, int $max): ?string
    {
        $text = trim((string) $value);

        return $text === '' ? null : mb_substr($text, 0, $max);
    }
}
