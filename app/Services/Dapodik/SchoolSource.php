<?php

namespace App\Services\Dapodik;

/**
 * Sumber data sekolah eksternal (berkas CSV hasil ekspor Dapodik, atau webservice Dapodik).
 * Setiap sumber menghasilkan baris ternormalisasi dengan kunci internal (lihat SchoolRecordMapper).
 */
interface SchoolSource
{
    /** Nama singkat sumber untuk log dan pesan konsol. */
    public function name(): string;

    /**
     * @return iterable<int, array<string, mixed>>  baris ternormalisasi
     *
     * @throws \RuntimeException bila sumber belum dikonfigurasi atau tidak dapat dibaca
     */
    public function records(): iterable;
}
