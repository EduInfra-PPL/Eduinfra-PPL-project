<?php

namespace App\Services\Wilayah;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use RuntimeException;

/**
 * Klien REST wilayah-id (batas administrasi BIG + kode Kemendagri).
 * Endpoint berat dibatasi 10/menit tanpa API key; klien menunggu sesuai Retry-After.
 *
 * @see https://github.com/dhanyyudi/wilayah-id
 */
class WilayahIdClient
{
    public function __construct(
        private string $baseUrl = 'https://wilayah-id-api.dhanypedia.com/api/v1',
        private int $pauseSeconds = 7,
    ) {}

    /** @return array<string, mixed> */
    public function get(string $path, array $query = []): array
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $response = Http::withHeaders(['User-Agent' => 'EduInfra-Tracker/1.0 (sinkronisasi batas wilayah)'])
                ->acceptJson()
                ->timeout(180)
                ->withOptions(['verify' => config('eduinfra.ca_bundle', true)])
                ->get($this->baseUrl.'/'.ltrim($path, '/'), $query);

            if ($response->successful()) {
                usleep($this->pauseSeconds * 1_000_000);

                return $response->json() ?? [];
            }

            if (! $this->shouldRetry($response)) {
                throw new RuntimeException("wilayah-id {$path} gagal (HTTP {$response->status()}).");
            }

            sleep($this->retryAfter($response));
        }

        throw new RuntimeException("wilayah-id {$path} gagal setelah beberapa kali percobaan.");
    }

    private function shouldRetry(Response $response): bool
    {
        return $response->status() === 429 || $response->status() >= 500;
    }

    private function retryAfter(Response $response): int
    {
        return max(5, (int) ($response->header('Retry-After') ?: 30));
    }
}
