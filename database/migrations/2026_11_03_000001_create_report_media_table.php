<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Domain 2: Pelaporan Kerusakan dan Verifikasi Lapangan (PBI-05 / FR-05)
     * Tabel: report_media (Unggahan Media Bukti Fisik & Validasi EXIF GPS)
     */
    public function up(): void
    {
        Schema::create('report_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')
                ->constrained('damage_reports')
                ->cascadeOnDelete();

            $table->string('media_path', 255)->comment('Path relatif penyimpanan file di disk public');
            $table->string('file_name', 255)->comment('Nama berkas asli saat diunggah');
            $table->unsignedBigInteger('file_size')->default(0)->comment('Ukuran berkas dalam bytes');
            $table->string('mime_type', 50)->default('image/jpeg');

            // Metadata Geospasial & EXIF GPS
            $table->decimal('exif_lat', 10, 7)->nullable()->comment('Latitude desimal dari metadata EXIF WGS84');
            $table->decimal('exif_lng', 10, 7)->nullable()->comment('Longitude desimal dari metadata EXIF WGS84');
            $table->timestamp('captured_at')->nullable()->comment('Waktu asli pemotretan dari EXIF DateTimeOriginal');
            $table->unsignedInteger('distance_meters')->nullable()->comment('Deviasi jarak terhadap koordinat master sekolah');
            $table->boolean('is_verified_location')->default(false)->comment('True jika deviasi <= 250 meter (Protokol Anti-Fraud)');

            $table->timestamps();

            // Indeks untuk pencarian cepat & audit
            $table->index('report_id');
            $table->index('is_verified_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_media');
    }
};

