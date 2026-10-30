<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Domain 2: Pelaporan Kerusakan dan Verifikasi Lapangan (PBI-04 / FR-04)
     * Tabel: damage_reports
     */
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_num', 30)->unique();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('facility_categories')->restrictOnDelete();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('title', 200);
            $table->text('description');
            $table->unsignedTinyInteger('severity_level')->default(1)->comment('Skala 1 (Ringan) s/d 5 (Kritis)');
            $table->string('status', 20)->default('pending')->comment('pending, verified, rejected, in_repair, resolved');
            
            $table->timestamps();

            // Indeks untuk pencarian dan filter cepat (PBI-04)
            $table->index('status');
            $table->index('severity_level');
            $table->index('school_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};

