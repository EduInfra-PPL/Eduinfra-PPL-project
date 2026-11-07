<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Registrasi akun (Domain 5): kolom profil pendaftar pada tabel users.
|
| - phone             : nomor seluler untuk token OTP (users.phone pada ERD)
| - role_enum         : persona RBAC yang diminta (users.role_enum pada ERD)
| - registration_role : kartu peran yang dipilih pada form registrasi (pilihan asli pendaftar)
| - organization      : nama satdik / satuan kerja unit asal (users.organization pada ERD)
| - region_id         : wilayah tugas koridor 3T (FK ke regions)
| - terms_accepted_at : waktu persetujuan Pakta Integritas Kerahasiaan Data Sarpras 3T
|
| Migration baru: tidak mengubah migration users yang sudah ada.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
            $table->string('role_enum', 30)->nullable()->index();
            $table->string('registration_role', 30)->nullable();
            $table->string('organization', 150)->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->timestamp('terms_accepted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('region_id');
            $table->dropIndex(['role_enum']);
            $table->dropColumn(['phone', 'role_enum', 'registration_role', 'organization', 'terms_accepted_at']);
        });
    }
};