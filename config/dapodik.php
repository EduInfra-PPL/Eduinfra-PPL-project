<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sumber data sekolah eksternal (Dapodik)
    |--------------------------------------------------------------------------
    | Dipakai oleh perintah `php artisan schools:sync`.
    | Akses webservice Dapodik harus diajukan resmi ke Pusdatin (Pelayanan Data).
    | Jangan menyimpan token di repositori; isi lewat .env.
    */

    'csv' => [
        'path' => env('DAPODIK_CSV_PATH'),
        'delimiter' => env('DAPODIK_CSV_DELIMITER', ','),
    ],

    'webservice' => [
        'base_url' => env('DAPODIK_BASE_URL'),
        'token' => env('DAPODIK_TOKEN'),
        'schools_path' => env('DAPODIK_SCHOOLS_PATH'),
        'data_key' => env('DAPODIK_DATA_KEY', 'data'),
        'page_param' => env('DAPODIK_PAGE_PARAM', 'page'),
        'per_page_param' => env('DAPODIK_PER_PAGE_PARAM', 'per_page'),
        'per_page' => (int) env('DAPODIK_PER_PAGE', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Peta field: kunci internal => nama kolom/field di sumber
    |--------------------------------------------------------------------------
    | Sesuaikan dengan header CSV ekspor Dapodik atau nama field webservice resmi.
    | Kunci internal wajib ada: npsn, name, educational_level, lat, lng.
    | region_code = kode wilayah kabupaten (format sama dengan regions.code, mis. "53.05").
    */

    'fields' => [
        'npsn' => 'npsn',
        'name' => 'name',
        'educational_level' => 'educational_level',
        'address' => 'address',
        'lat' => 'lat',
        'lng' => 'lng',
        'region_code' => 'region_code',
        'accreditation' => 'accreditation',
        'contact_phone' => 'contact_phone',
        'contact_email' => 'contact_email',
    ],

];
