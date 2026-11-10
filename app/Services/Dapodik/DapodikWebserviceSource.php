<?php

namespace App\Services\Dapodik;

use Generator;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien webservice Dapodik untuk data sekolah.
 *
 * PERHATIAN: Kemendikdasmen belum mempublikasikan dokumentasi API publik untuk Dapodik.
 * Akses resmi diajukan lewat Pusdatin (Pelayanan Data). Kelas ini hanya berjalan setelah
 * base_url, token, dan schools_path diisi dari spesifikasi resmi yang diberikan. Parameter
 * paginasi (page/per_page) dan kunci data (data_key) juga harus disesuaikan dengan spesifikasi itu.
 */
class DapodikWebserviceSource implements SchoolSource
{
    /** @param array<string, mixed> $config  isi dari config('dapodik.webservice') */
    public function __construct(
        private SchoolRecordMapper $mapper,
        private array $config,
    ) {}

    public function name(): string
    {
        return 'dapodik-webservice';
    }

    /** @return Generator<int, array<string, mixed>> */
    public function records(): Generator
    {
        $baseUrl = rtrim((string) ($this->config['base_url'] ?? ''), '/');
        $token = (string) ($this->config['token'] ?? '');
        $path = ltrim((string) ($this->config['schools_path'] ?? ''), '/');

        if ($baseUrl === '' || $token === '' || $path === '') {
            throw new RuntimeException(
                'Webservice Dapodik belum dikonfigurasi. Isi DAPODIK_BASE_URL, DAPODIK_TOKEN, dan '
                .'DAPODIK_SCHOOLS_PATH setelah akses resmi dari Pusdatin tersedia.'
            );
        }

        $perPage = max(1, (int) ($this->config['per_page'] ?? 500));
        $page = 1;

        do {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(60)
                ->retry(2, 500)
                ->get($baseUrl.'/'.$path, [
                    ($this->config['page_param'] ?? 'page') => $page,
                    ($this->config['per_page_param'] ?? 'per_page') => $perPage,
                ]);

            if ($response->failed()) {
                // jangan sertakan token atau isi respons di pesan error
                throw new RuntimeException('Permintaan ke webservice Dapodik gagal (HTTP '.$response->status().').');
            }

            $items = data_get($response->json(), $this->config['data_key'] ?? 'data', []);
            if (! is_array($items) || $items === []) {
                break;
            }

            foreach ($items as $item) {
                if (is_array($item)) {
                    yield $this->mapper->map($item);
                }
            }

            $page++;
        } while (count($items) >= $perPage);
    }
}
