<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Middleware untuk endpoint tulis Domain 1 (hanya Admin Dinas)
    |--------------------------------------------------------------------------
    | Tabel 15 proposal: baca = publik, tulis = Admin. Default deny-by-default
    | (NFR-03): login (auth) + peran Admin Dinas (alias `admin.dinas`, lihat
    | bootstrap/app.php). Rute API tidak memakai sesi, sehingga tulis lewat API
    | ditolak (401) sampai autentikasi API (mis. Sanctum) dipasang.
    |
    | Untuk dev lokal SEBELUM auth siap, kosongkan lewat .env:
    |     EDUINFRA_WRITE_MIDDLEWARE=
    | Jangan dikosongkan di staging/produksi.
    */
    'write_middleware' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('EDUINFRA_WRITE_MIDDLEWARE', 'auth,admin.dinas'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Berkas CA untuk HTTPS keluar (sinkronisasi wilayah-id / Overpass)
    |--------------------------------------------------------------------------
    | Kosongkan untuk memakai verifikasi default. Isi bila PHP di mesin Anda
    | tidak punya sertifikat root (contoh Windows): path ca-bundle.crt.
    */
    'ca_bundle' => env('EDUINFRA_CA_BUNDLE') ?: true,

];
