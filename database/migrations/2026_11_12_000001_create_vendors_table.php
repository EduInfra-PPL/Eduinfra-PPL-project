<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 200);
            $table->string('entity_type', 30)->default('CV');
            $table->string('license_num', 100)->unique();
            $table->string('nib', 50)->nullable();
            $table->string('contact_phone', 50);
            $table->foreignId('service_region_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();
            $table->string('grade', 15)->default('K3');
            $table->string('qualification_label', 150)->default('Kecil - Bangunan Pendidikan');
            $table->decimal('max_contract_limit', 15, 2)->default(2500000000);
            $table->string('sbu_status', 30)->default('verified');
            $table->date('sbu_valid_until')->nullable();
            $table->string('specialty', 150)->nullable();
            $table->string('logistics_note', 255)->nullable();
            $table->string('lead_engineer', 150)->nullable();
            $table->string('engineer_role', 150)->nullable();
            $table->decimal('rating', 3, 2)->default(4.75);
            $table->unsignedInteger('review_count')->default(5);
            $table->decimal('on_time_pct', 5, 2)->default(95.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_region_id', 'is_active']);
            $table->index(['grade', 'sbu_status']);
        });

        if (Schema::hasTable('repair_projects') && Schema::hasColumn('repair_projects', 'vendor_id')) {
            Schema::table('repair_projects', function (Blueprint $table) {
                $table->foreign('vendor_id')
                    ->references('id')
                    ->on('vendors')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('repair_projects')) {
            Schema::table('repair_projects', function (Blueprint $table) {
                $table->dropForeign(['vendor_id']);
            });
        }

        Schema::dropIfExists('vendors');
    }
};
