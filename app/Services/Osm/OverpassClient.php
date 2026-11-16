<?php

namespace App\Services\Osm;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien Overpass API (OpenStreetMap) untuk mengambil amenity=school dalam satu bounding box.
 * Mengikuti kebijakan pemakaian wajar: User-Agent jelas, satu permintaan berurutan,
 * dan menunggu bila server membatasi (429/503/504).
 *
 * @see https://wiki.openstreetmap.org/wiki/Overpass_API
 */
class OverpassClient
{
    public function __construct(
        private string $endpoint = 'https://overpass-api.de/api/interpreter',
    ) {}

    /**
     * @return list<array<string, mixed>>  elemen Overpass (node/way/relation)
     */
    public function schoolsInBbox(float $south, float $west, float $north, float $east): array
    {
        $query = sprintf(
            '[out:json][timeout:600];nwr["amenity"="school"](%F,%F,%F,%F);out center tags;',
            $south, $west, $north, $east,
        );

        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $response = Http::withHeaders([
                'User-Agent' => 'EduInfra-Tracker/1.0 (sinkronisasi data sekolah; kontak: admin proyek)',
            ])
                ->asForm()
                ->timeout(700)
                ->withOptions(['verify' => config('eduinfra.ca_bundle', true)])
                ->post($this->endpoint, ['data' => $query]);

            if ($response->successful() && ! str_contains((string) ($response->json('remark') ?? ''), 'runtime error')) {
                return $response->json('elements') ?? [];
            }

            // error selain pembatasan/timeout server tidak perlu dicoba ulang
            if (! $response->successful() && ! in_array($response->status(), [429, 502, 503, 504], true)) {
                throw new RuntimeException("Overpass gagal (HTTP {$response->status()}).");
            }

            sleep(30 * $attempt);
        }

        throw new RuntimeException('Overpass gagal setelah beberapa kali percobaan (batas waktu atau pembatasan server).');
    }
}
