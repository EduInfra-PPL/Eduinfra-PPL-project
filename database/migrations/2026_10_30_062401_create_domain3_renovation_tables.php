<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('project_milestones');
        Schema::dropIfExists('cost_estimates');
        Schema::dropIfExists('renovation_projects');
        Schema::dropIfExists('repair_projects');

        Schema::create('repair_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code', 40)->unique();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('report_id')->nullable()->constrained('damage_reports')->nullOnDelete();
            // Kolom vendor_id disiapkan tanpa FK constraint karena modul vendors berada di luar cakupan Sprint 1.
            $table->unsignedBigInteger('vendor_id')->nullable()->index();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 30)->default('in_progress');
            $table->decimal('progress_pct', 5, 2)->default(0.00);
            $table->decimal('target_pct', 5, 2)->default(60.00);
            $table->decimal('budget_allocated', 15, 2)->default(0.00);
            $table->decimal('budget_spent', 15, 2)->default(0.00);
            $table->string('contractor_name', 200)->nullable();
            $table->string('consortium_name', 200)->nullable();
            $table->string('surveyor_name', 150)->nullable();
            $table->text('inspector_notes')->nullable();
            $table->string('priority', 20)->default('medium');

            $table->date('target_start')->nullable();
            $table->date('target_end')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('school_id');
            $table->index('report_id');
        });

        Schema::create('cost_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('repair_projects')->cascadeOnDelete();
            $table->foreignId('facility_category_id')->nullable()->constrained('facility_categories')->nullOnDelete();
            $table->string('item_code', 30)->nullable();
            $table->string('category_group', 150)->nullable();
            $table->string('item_name');
            $table->text('specs')->nullable();
            $table->string('unit', 40)->default('unit');
            $table->decimal('quantity', 12, 2)->default(1.00);
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->decimal('total_price', 15, 2)->default(0.00);
            $table->string('approval_status', 30)->default('Approved');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->index(['project_id', 'approval_status']);
        });

        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('repair_projects')->cascadeOnDelete();
            $table->string('milestone_code', 20)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('weight_pct', 5, 2)->default(0.00);
            $table->decimal('progress_pct', 5, 2)->default(0.00);
            $table->string('status', 30)->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('proof_media_url', 500)->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('surveyor_name', 150)->nullable();
            $table->text('notes')->nullable();
            $table->json('evidence_photos')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
        Schema::dropIfExists('cost_estimates');
        Schema::dropIfExists('renovation_projects');
        Schema::dropIfExists('repair_projects');
    }
};
