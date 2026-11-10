<?php

namespace App\Services\Osm;

/**
 * Mengubah elemen OpenStreetMap (node/way/relation amenity=school) menjadi baris sekolah.
 * Fungsi murni tanpa akses database, sehingga bisa diuji unit.
 */
class OsmSchoolMapper
{
    // Batas kasar wilayah Indonesia (lintang, bujur). Titik di luar batas ini dianggap salah.
    private const LAT_MIN = -11.5;
    private const LAT_MAX = 6.5;
    private const LNG_MIN = 94.5;
    private const LNG_MAX = 141.5;

    /**
     * @param  array<string, mixed>  $element  elemen Overpass (dengan `out center tags`)
     * @return array{record: array<string, mixed>|null, reason: string|null}
     */
    public function toRecord(array $element): array
    {
        $tags = $element['tags'] ?? [];
        $name = trim((string) ($tags['name'] ?? $tags['official_name'] ?? ''));
        if ($name === '') {
            return ['record' => null, 'reason' => 'tanpa_nama'];
        }

        $level = $this->educationalLevel($name, $tags);
        if ($level === null) {
            return ['record' => null, 'reason' => 'jenjang_tidak_dikenali'];
        }

        $coords = $this->coordinates($element);
        if ($coords === null) {
            return ['record' => null, 'reason' => 'koordinat_tidak_valid'];
        }

        $prefix = match ($element['type'] ?? '') {
            'node' => 'n',
            'way' => 'w',
            'relation' => 'r',
            default => null,
        };
        if ($prefix === null || ! isset($element['id'])) {
            return ['record' => null, 'reason' => 'elemen_tidak_valid'];
        }

        return [
            'record' => [
                'osm_id' => $prefix.$element['id'],
                'name' => mb_substr($name, 0, 200),
                'educational_level' => $level,
                'address' => $this->address($tags),
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'contact_phone' => $this->phone($tags),
                'contact_email' => $this->email($tags),
            ],
            'reason' => null,
        ];
    }

    /**
     * Jenjang SD/SMP/SMA. Nama didahulukan (lebih sering diisi), lalu tag isced:level.
     * Jenjang prasekolah (TK, PAUD, KB) dan SLB tidak dimasukkan (null).
     *
     * @param  array<string, string>  $tags
     */
    public function educationalLevel(string $name, array $tags): ?string
    {
        $upper = mb_strtoupper(trim($name));

        if (preg_match('/^(SMP|SMPN|SMPS|SMPIT|MTS|MTSN|MTSS)\b/u', $upper)) {
            return 'SMP';
        }
        if (preg_match('/^(SMA|SMAN|SMAS|SMAIT|SMK|SMKN|SMKS|MA|MAN|MAS|MAK)\b/u', $upper)) {
            return 'SMA';
        }
        if (preg_match('/^(SD|SDN|SDS|SDIT|SDI|SDK|MI|MIN|MIS|MIT)\b/u', $upper)) {
            return 'SD';
        }

        $isced = trim(explode(';', (string) ($tags['isced:level'] ?? ''))[0]);

        return match ($isced) {
            '1' => 'SD',
            '2' => 'SMP',
            '3' => 'SMA',
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $element
     * @return array{lat: float, lng: float}|null
     */
    public function coordinates(array $element): ?array
    {
        $lat = $element['lat'] ?? $element['center']['lat'] ?? null;
        $lng = $element['lon'] ?? $element['center']['lon'] ?? null;
        if (! is_numeric($lat) || ! is_numeric($lng)) {
            return null;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;
        if ($lat < self::LAT_MIN || $lat > self::LAT_MAX || $lng < self::LNG_MIN || $lng > self::LNG_MAX) {
            return null;
        }

        return ['lat' => $lat, 'lng' => $lng];
    }

    /** @param  array<string, string>  $tags */
    public function address(array $tags): ?string
    {
        if (filled($tags['addr:full'] ?? null)) {
            return mb_substr(trim($tags['addr:full']), 0, 1000);
        }

        $parts = array_filter([
            $tags['addr:street'] ?? null,
            $tags['addr:housenumber'] ?? null,
            $tags['addr:village'] ?? $tags['addr:subdistrict'] ?? null,
            $tags['addr:district'] ?? null,
            $tags['addr:city'] ?? null,
        ], fn ($v): bool => filled($v));

        return $parts === [] ? null : mb_substr(implode(', ', array_map('trim', $parts)), 0, 1000);
    }

    /** @param  array<string, string>  $tags */
    public function phone(array $tags): ?string
    {
        $value = trim((string) ($tags['phone'] ?? $tags['contact:phone'] ?? ''));

        return $value === '' ? null : mb_substr($value, 0, 30);
    }

    /** @param  array<string, string>  $tags */
    public function email(array $tags): ?string
    {
        $value = trim((string) ($tags['email'] ?? $tags['contact:email'] ?? ''));

        return ($value !== '' && mb_strlen($value) <= 150 && filter_var($value, FILTER_VALIDATE_EMAIL))
            ? $value
            : null;
    }
}
