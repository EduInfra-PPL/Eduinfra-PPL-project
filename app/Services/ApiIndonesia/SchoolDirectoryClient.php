<?php

namespace App\Services\ApiIndonesia;

use Generator;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien API Indonesia untuk direktori sekolah (GET /api/v1/sekolah).
 * Autentikasi lewat header x-api-key. Paginasi: page dan per_page (maksimum 200).
 *
 * @see https://docs.apiindonesia.id/en/#sekolah
 */
class SchoolDirectoryClient
{
    public function __construct(
        private ?string $baseUrl = null,
        private ?string $apiKey = null,
        private int $perPage = 200,
    ) {}

    /**
     * Seluruh sekolah, halaman demi halaman. Tiap pemanggilan API dihitung kredit oleh penyedia.
     *
     * @param  array<string, string>  $filters  mis. ['jenis' => 'SD', 'province_id' => '32']
     * @return Generator<int, array<string, mixed>>
     */
    public function schools(array $filters = []): Generator
    {
        $key = $this->apiKey ?? config('services.apiindonesia.key');
        if (blank($key)) {
            throw new RuntimeException('API Indonesia belum dikonfigurasi. Isi APIINDONESIA_KEY di .env dengan key dari dashboard apiindonesia.id.');
        }

        $base = rtrim($this->baseUrl ?? config('services.apiindonesia.base_url'), '/');
        $page = 1;

        do {
            $response = $this->get($base.'/sekolah', $key, array_merge($filters, [
                'page' => $page,
                'per_page' => $this->perPage,
            ]));

            $items = $response->json('data') ?? [];
            foreach ($items as $item) {
                if (is_array($item)) {
                    yield $item;
                }
            }

            // pakai meta.total_pages bila ada; bila tidak, lanjut selama halaman terisi penuh
            $totalPages = $response->json('meta.total_pages');
            $hasMore = $totalPages !== null
                ? $page < (int) $totalPages
                : count($items) >= $this->perPage;
            $page++;
        } while ($items !== [] && $hasMore);
    }

    private function get(string $url, string $key, array $query): Response
    {
        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $response = Http::withHeaders(['x-api-key' => $key, 'Accept' => 'application/json'])
                ->timeout(60)
                ->withOptions(['verify' => config('eduinfra.ca_bundle', true)])
                ->get($url, $query);

            if ($response->successful()) {
                return $response;
            }

            if ($response->status() === 401 || $response->status() === 403) {
                // jangan tampilkan key; beri petunjuk perbaikan saja
                throw new RuntimeException('API Indonesia menolak key (HTTP '.$response->status().'). Periksa APIINDONESIA_KEY di .env.');
            }

            if (! in_array($response->status(), [429, 500, 502, 503, 504], true)) {
                throw new RuntimeException('API Indonesia gagal (HTTP '.$response->status().').');
            }

            sleep((int) ($response->header('Retry-After') ?: 5 * $attempt));
        }

        throw new RuntimeException('API Indonesia gagal setelah beberapa kali percobaan.');
    }
}
