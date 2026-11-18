<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Domain 4: Pendanaan Bantuan, CSR & Pencairan Anggaran.
     * Entitas 12: Contribution Log (aid_contributions) — PBI-11.
     */
    public function up(): void
    {
        Schema::create('aid_contributions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 30)->unique();
            $table->foreignId('campaign_id')->constrained('aid_campaigns')->cascadeOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('donor_name', 150);
            $table->string('donor_unit', 150)->nullable();
            $table->unsignedBigInteger('amount_or_goods');
            $table->string('contribution_type', 20)->default('cash')->comment('cash, goods');
            $table->string('payment_proof', 255)->nullable();
            $table->string('term_stage', 50)->default('Termin 1 (Pondasi)');
            $table->string('verification_note', 120)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('verified')->comment('pending, verified, rejected');
            $table->timestamps();

            $table->index('status');
            $table->index('donor_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aid_contributions');
    }
};
