<?php

namespace App\Services\Media;

use Carbon\Carbon;

/**
 * Service untuk ekstraksi metadata binary EXIF foto bukti fisik kerusakan.
 * Mendukung pembacaan GPS Latitude, Longitude, dan Waktu Pemotretan Asli.
 * Mematuhi prinsip Single Responsibility (AGENT_RULE.md).
 */
class ExifExtractionService
{
    /**
     * Ekstrak metadata geolokasi dan waktu dari berkas gambar.
     *
     * @param string $filePath Path absolut atau relatif berkas gambar di sistem
     * @return array{
     *     has_gps: bool,
     *     latitude: float|null,
     *     longitude: float|null,
     *     captured_at: Carbon|null,
     *     raw_exif: array<string, mixed>
     * }
     */
    public function extract(string $filePath): array
    {
        $defaultResult = [
            'has_gps' => false,
            'latitude' => null,
            'longitude' => null,
            'captured_at' => null,
            'raw_exif' => [],
        ];

        if (!file_exists($filePath) || !is_readable($filePath)) {
            return $defaultResult;
        }

        // Baca header EXIF gambar menggunakan modul native PHP
        // Matikan error warning PHP dengan @ jika berkas JPEG tidak memiliki header EXIF
        $exif = @exif_read_data($filePath);

        if (!$exif || !is_array($exif)) {
            return $defaultResult;
        }

        $latitude = null;
        $longitude = null;
        $hasGps = false;

        // Periksa apakah terdapat metadata GPS Latitude dan Longitude
        if (
            isset($exif['GPSLatitude'], $exif['GPSLatitudeRef'], $exif['GPSLongitude'], $exif['GPSLongitudeRef']) &&
            is_array($exif['GPSLatitude']) &&
            is_array($exif['GPSLongitude'])
        ) {
            $lat = $this->convertDmsToDecimal($exif['GPSLatitude'], (string) $exif['GPSLatitudeRef']);
            $lng = $this->convertDmsToDecimal($exif['GPSLongitude'], (string) $exif['GPSLongitudeRef']);

            if ($lat !== null && $lng !== null) {
                $latitude = round($lat, 7);
                $longitude = round($lng, 7);
                $hasGps = true;
            }
        }

        // Ekstrak waktu pemotretan asli (DateTimeOriginal atau DateTimeDigitized)
        $capturedAt = null;
        $dateString = $exif['DateTimeOriginal'] ?? $exif['DateTimeDigitized'] ?? $exif['DateTime'] ?? null;

        if ($dateString && is_string($dateString)) {
            try {
                // Format EXIF standar: 'YYYY:MM:DD HH:MM:SS'
                $capturedAt = Carbon::createFromFormat('Y:m:d H:i:s', trim($dateString));
            } catch (\Throwable) {
                try {
                    $capturedAt = Carbon::parse($dateString);
                } catch (\Throwable) {
                    $capturedAt = null;
                }
            }
        }

        return [
            'has_gps' => $hasGps,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'captured_at' => $capturedAt,
            'raw_exif' => $exif,
        ];
    }

    /**
     * Konversi koordinat GPS format Derajat-Menit-Detik (DMS) menjadi Desimal WGS84.
     *
     * @param array<int, string|float|int> $dmsArray Array [Derajat, Menit, Detik] dalam pecahan rasional EXIF
     * @param string $hemisphereRef Referensi belahan bumi ('N', 'S', 'E', 'W')
     * @return float|null
     */
    public function convertDmsToDecimal(array $dmsArray, string $hemisphereRef): ?float
    {
        if (count($dmsArray) < 3) {
            return null;
        }

        $degrees = $this->evaluateExifFraction($dmsArray[0]);
        $minutes = $this->evaluateExifFraction($dmsArray[1]);
        $seconds = $this->evaluateExifFraction($dmsArray[2]);

        if ($degrees === null || $minutes === null || $seconds === null) {
            return null;
        }

        // Rumus Desimal WGS84: Derajat + (Menit / 60) + (Detik / 3600)
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        // Belahan bumi Selatan (S) dan Barat (W) bernilai negatif
        $hemisphereRef = strtoupper(trim($hemisphereRef));
        if ($hemisphereRef === 'S' || $hemisphereRef === 'W') {
            $decimal = -$decimal;
        }

        return $decimal;
    }

    /**
     * Evaluasi pecahan string/angka rasional EXIF (contoh: "109/1", "18/1", "2450/100").
     */
    protected function evaluateExifFraction(mixed $part): ?float
    {
        if (is_numeric($part)) {
            return (float) $part;
        }

        if (is_string($part)) {
            $parts = explode('/', trim($part));
            if (count($parts) === 2) {
                $numerator = (float) $parts[0];
                $denominator = (float) $parts[1];
                if ($denominator != 0) {
                    return $numerator / $denominator;
                }
            } elseif (count($parts) === 1 && is_numeric($parts[0])) {
                return (float) $parts[0];
            }
        }

        return null;
    }
}

