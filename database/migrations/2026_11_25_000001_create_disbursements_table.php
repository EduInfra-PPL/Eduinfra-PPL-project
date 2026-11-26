<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Domain 4: Pendanaan Bantuan, CSR & Pencairan Anggaran.
     * Entitas 13: Disbursement Log (disbursements) — PBI-12.
     */
    public function up(): void
    {
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 35)->unique();
            $table->foreignId('project_id')->constrained('repair_projects')->cascadeOnDelete();
            $table->unsignedTinyInteger('stage_num')->comment('1 = Termin 1 (30%), 2 = Termin 2 (40%), 3 = Termin 3 (30%)');
            $table->unsignedBigInteger('amount');
            $table->string('receipt_file', 255)->nullable();
            $table->string('tax_invoice_file', 255)->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('vendor_name', 150)->nullable();
            $table->string('vendor_bank_account', 100)->nullable();
            $table->string('status', 20)->default('disbursed')->comment('draft, pending_approval, disbursed, cancelled');
            $table->timestamps();

            $table->index(['project_id', 'stage_num']);
            $table->index('status');
            $table->index('disbursed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disbursements');
    }
};
