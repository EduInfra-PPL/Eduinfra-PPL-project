<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| PBI-13 (Login): kolom tambahan pada tabel users.
|
| - identity_number : NIP (18 digit) atau NIK (16 digit). Dipakai sebagai alternatif
|                     email pada form login ("NIP / Email Kedinasan").
| - is_verified     : akun sudah diverifikasi (users.is_verified pada ERD).
|                     Default true agar akun yang sudah ada tetap dapat login; alur
|                     registrasi mandiri (PBI lain) wajib mengisi false sampai akun
|                     disetujui administrator.
|
| Migration baru: tidak mengubah migration users bawaan.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_number', 18)->nullable()->unique();
            $table->boolean('is_verified')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['identity_number']);
            $table->dropColumn(['identity_number', 'is_verified']);
        });
    }
};
