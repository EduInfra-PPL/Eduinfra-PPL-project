<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Domain 4: Pendanaan Bantuan, CSR & Pencairan Anggaran.
     * Entitas 11: Aid Campaign (aid_campaigns).
     *
     * CATATAN INTEGRASI ANTAR-DOMAIN:
     * Kolom `project_id` merujuk ke `repair_projects.id` (Domain 3).
     * Sengaja belum dipasang `->constrained('repair_projects')` di migration ini
     * karena tabel Domain 3 dikerjakan terpisah agar branch Domain 4 dapat
     * dimigrasi secara independen tanpa error maupun merge conflict.
     * Relasi 1:1 (1 proyek maksimal 1 kampanye aktif) dijaga via unique index.
     */
    public function up(): void
    {
        Schema::create('aid_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->unique();
            $table->string('title', 200);
            $table->unsignedBigInteger('target_amount');
            $table->unsignedBigInteger('collected_amount')->default(0);
            $table->date('deadline');
            $table->string('status', 20)->default('open')->comment('draft, open, funded, closed');
            $table->timestamps();

            $table->index('status');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aid_campaigns');
    }
};
