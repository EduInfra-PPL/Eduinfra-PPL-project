<?php

namespace App\Services\Spatial;

/**
 * Service kalkulasi deviasi jarak spasial geodetik dan validasi batas geofence.
 * Mengimplementasikan Formula Haversine (Great-Circle Distance) pada bola bumi WGS84.
 * Mematuhi prinsip Single Responsibility dan batasan toleransi 250 meter (AGENT_RULE.md).
 */
class SpatialDistanceService
{
    /**
     * Radius rata-rata bumi dalam satuan meter (WGS84 spherical approximation).
     */
    public const EARTH_RADIUS_METERS = 6371000;

    /**
     * Batas toleransi radius spasial maksimum dari titik master sekolah sesuai PRD Domain 2.
     */
    public const GEOFENCE_TOLERANCE_METERS = 250;

    /**
     * Hitung deviasi jarak antara dua titik koordinat lintang & bujur (WGS84).
     *
     * @param float $lat1 Lintang titik 1 (misal: Latitude GPS Foto EXIF)
     * @param float $lng1 Bujur titik 1 (misal: Longitude GPS Foto EXIF)
     * @param float $lat2 Lintang titik 2 (misal: Latitude Master Sekolah)
     * @param float $lng2 Bujur titik 2 (misal: Longitude Master Sekolah)
     * @return int Jarak dalam satuan meter (dibulatkan ke integer terdekat)
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        // Konversi derajat ke radian
        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        // Selisih koordinat
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLng = $lng2Rad - $lng1Rad;

        // Rumus Haversine: a = sin²(Δlat/2) + cos(lat1) * cos(lat2) * sin²(Δlng/2)
        $a = sin($deltaLat / 2) ** 2 +
            cos($lat1Rad) * cos($lat2Rad) * (sin($deltaLng / 2) ** 2);

        // c = 2 * atan2(√a, √(1−a))
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Jarak (d) = R * c
        $distance = self::EARTH_RADIUS_METERS * $c;

        return (int) round($distance);
    }

    /**
     * Evaluasi apakah deviasi jarak berada dalam toleransi radius geofence resmi (<= 250 meter).
     *
     * @param int|float|null $distanceInMeters
     * @return bool True jika deviasi <= 250 meter, False jika deviasi > 250m atau null
     */
    public function isWithinGeofence(int|float|null $distanceInMeters): bool
    {
        if ($distanceInMeters === null) {
            return false;
        }

        return $distanceInMeters <= self::GEOFENCE_TOLERANCE_METERS;
    }
}

