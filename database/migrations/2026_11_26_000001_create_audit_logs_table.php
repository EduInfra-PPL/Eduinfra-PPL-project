<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel: audit_logs (Domain 2: PBI-06 - Log Verifikasi & Inspeksi Lapangan Auditor)
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')
                ->constrained('damage_reports')
                ->cascadeOnDelete();
            $table->foreignId('auditor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('inspection_date')->comment('Tanggal pelaksanaan inspeksi/visitasi lapangan');
            $table->decimal('physical_score', 3, 2)->comment('Skor kondisi fisik hasil inspeksi (skala 0.00 - 5.00)');
            $table->string('audit_status', 30)
                ->default('Needs_Review')
                ->comment('Approved, Needs_Review, Rejected');
            $table->text('notes')->nullable()->comment('Catatan resmi auditor terkait temuan lapangan');

            $table->timestamps();

            $table->index('report_id');
            $table->index('auditor_id');
            $table->index('audit_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
