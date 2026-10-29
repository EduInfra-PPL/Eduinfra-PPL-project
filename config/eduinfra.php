<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Middleware untuk endpoint tulis Domain 1 (hanya Admin Dinas)
    |--------------------------------------------------------------------------
    | Tabel 15 proposal: baca = publik, tulis = Admin. Default deny-by-default
    | (NFR-03): wajib login Sanctum + peran admin. Middleware alias `role` berasal
    | dari modul RBAC (FR-14 / Domain 5) dan harus tersedia sebelum dipakai.
    |
    | Untuk dev lokal SEBELUM auth siap, kosongkan lewat .env:
    |     EDUINFRA_WRITE_MIDDLEWARE=
    | Jangan dikosongkan di staging/produksi.
    */
    'write_middleware' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('EDUINFRA_WRITE_MIDDLEWARE', 'auth:sanctum,role:admin'))
    ))),

];
