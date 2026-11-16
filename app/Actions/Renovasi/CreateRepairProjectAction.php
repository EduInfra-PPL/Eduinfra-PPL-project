<?php

namespace App\Actions\Renovasi;

use App\Models\CostEstimate;
use App\Models\DamageReport;
use App\Models\FacilityCategory;
use App\Models\ProjectMilestone;
use App\Models\RepairProject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateRepairProjectAction
{
    public function execute(array $data): RepairProject
    {
        return DB::transaction(function () use ($data) {
            $damageReport = null;
            $categoryId = $data['facility_category_id'] ?? null;

            if (! empty($data['report_id'])) {
                $damageReport = DamageReport::with('category')->findOrFail($data['report_id']);
                $data['school_id'] = $damageReport->school_id;
                $categoryId = $categoryId ?: $damageReport->category_id;
                $damageReport->update(['status' => 'in_repair']);
            }

            $category = $categoryId
                ? FacilityCategory::find($categoryId)
                : FacilityCategory::where('is_enabled', true)->orderByDesc('urgency_weight')->first();

            $count = RepairProject::count() + 1;
            do {
                $projectCode = 'PRI-2026-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
                $count++;
            } while (RepairProject::where('project_code', $projectCode)->exists());

            $budget = (float) $data['budget_allocated'];
            $startDate = ! empty($data['target_start']) ? Carbon::parse($data['target_start']) : now();
            $endDate = ! empty($data['target_end']) ? Carbon::parse($data['target_end']) : $startDate->copy()->addDays(120);
            $totalDays = max(30, $startDate->diffInDays($endDate));

            $project = RepairProject::create([
                'project_code' => $projectCode,
                'school_id' => $data['school_id'],
                'report_id' => $damageReport?->id,
                'title' => $data['title'],
                'description' => ($data['description'] ?? null) ?: ($damageReport?->description ?? $data['title']),
                'status' => 'in_progress',
                'progress_pct' => 0.00,
                'target_pct' => 20.00,
                'budget_allocated' => $budget,
                'budget_spent' => 0.00,
                'contractor_name' => ($data['contractor_name'] ?? null) ?: 'Kontraktor Rekanan LPJK Wilayah',
                'consortium_name' => ($data['consortium_name'] ?? null) ?: 'Pendanaan APBD & Mitra CSR',
                'surveyor_name' => ($data['surveyor_name'] ?? null) ?: 'Tim Teknis Sarpras Dinas',
                'inspector_notes' => 'Proyek baru diinisiasi dan siap memulai pekerjaan persiapan tapak.',
                'priority' => $data['priority'] ?? 'medium',
                'target_start' => $startDate->toDateString(),
                'target_end' => $endDate->toDateString(),
            ]);

            $catLabel = $category
                ? ($category->sub_category ? "{$category->name} - {$category->sub_category}" : $category->name)
                : 'Fasilitas Sekolah';
            $defaultUnit = $category?->default_unit ?: 'paket';

            $milestones = [
                [
                    'milestone_code' => 'MS-01',
                    'title' => 'Pembersihan & Pembongkaran Struktur Awal',
                    'description' => "Persiapan tapak kerja dan pembongkaran bagian {$catLabel} yang rusak.",
                    'weight_pct' => 20.00,
                    'progress_pct' => 0.00,
                    'status' => 'in_progress',
                    'due_date' => $startDate->copy()->addDays((int) round($totalDays * 0.20))->toDateString(),
                    'surveyor_name' => $project->surveyor_name,
                ],
                [
                    'milestone_code' => 'MS-02',
                    'title' => 'Pekerjaan Struktur Utama & Rangka Konstruksi SNI',
                    'description' => "Pengerjaan konstruksi inti {$catLabel} sesuai standar teknis bangunan pendidikan.",
                    'weight_pct' => 35.00,
                    'progress_pct' => 0.00,
                    'status' => 'pending',
                    'due_date' => $startDate->copy()->addDays((int) round($totalDays * 0.55))->toDateString(),
                    'surveyor_name' => $project->surveyor_name,
                ],
                [
                    'milestone_code' => 'MS-03',
                    'title' => 'Pemasangan Penutup, Sanitasi & Utilitas Pendukung',
                    'description' => 'Pemasangan komponen pelindung bangunan, perapihan dinding, dan jaringan sanitasi.',
                    'weight_pct' => 25.00,
                    'progress_pct' => 0.00,
                    'status' => 'pending',
                    'due_date' => $startDate->copy()->addDays((int) round($totalDays * 0.80))->toDateString(),
                    'surveyor_name' => $project->surveyor_name,
                ],
                [
                    'milestone_code' => 'MS-04',
                    'title' => 'Finishing, Uji Kelayakan Fungsi & Serah Terima (BAST)',
                    'description' => 'Pengecatan akhir, pengujian fungsi fasilitas bersama komite sekolah, dan BAST.',
                    'weight_pct' => 20.00,
                    'progress_pct' => 0.00,
                    'status' => 'pending',
                    'due_date' => $endDate->toDateString(),
                    'surveyor_name' => $project->surveyor_name,
                ],
            ];

            foreach ($milestones as $ms) {
                $ms['project_id'] = $project->id;
                ProjectMilestone::create($ms);
            }

            $rabItems = [
                [
                    'item_code' => '1.1',
                    'category_group' => 'I. PEKERJAAN PERSIAPAN & PEMBONGKARAN',
                    'item_name' => "Pembongkaran & Pembersihan Area {$catLabel}",
                    'specs' => 'Pembersihan material lama dan pengamanan struktur sekitar ruang belajar.',
                    'unit' => 'paket',
                    'quantity' => 1,
                    'unit_price' => round($budget * 0.15, 0),
                    'total_price' => round($budget * 0.15, 0),
                ],
                [
                    'item_code' => '2.1',
                    'category_group' => 'II. PEKERJAAN STRUKTUR & KONSTRUKSI UTAMA',
                    'item_name' => "Rekonstruksi Komponen Utama {$catLabel}",
                    'specs' => 'Material berstandar SNI dengan perkuatan struktur tahan cuaca wilayah daerah.',
                    'unit' => $defaultUnit,
                    'quantity' => 1,
                    'unit_price' => round($budget * 0.45, 0),
                    'total_price' => round($budget * 0.45, 0),
                ],
                [
                    'item_code' => '3.1',
                    'category_group' => 'III. PEKERJAAN PELENGKAP & UTILITAS',
                    'item_name' => 'Pekerjaan Pelengkap, Dinding, Plafon & Sanitasi',
                    'specs' => 'Perapihan pasangan dinding, pelapis kedap air, dan saluran drainase.',
                    'unit' => $defaultUnit,
                    'quantity' => 1,
                    'unit_price' => round($budget * 0.25, 0),
                    'total_price' => round($budget * 0.25, 0),
                ],
                [
                    'item_code' => '4.1',
                    'category_group' => 'IV. FINISHING & UJI KELAYAKAN BAST',
                    'item_name' => 'Pengecatan, Instalasi Pendukung & Pengujian Fungsi',
                    'specs' => 'Finishing permukaan, instalasi kelistrikan, dan sertifikasi laik fungsi.',
                    'unit' => 'paket',
                    'quantity' => 1,
                    'unit_price' => $budget - round($budget * 0.15, 0) - round($budget * 0.45, 0) - round($budget * 0.25, 0),
                    'total_price' => $budget - round($budget * 0.15, 0) - round($budget * 0.45, 0) - round($budget * 0.25, 0),
                ],
            ];

            foreach ($rabItems as $item) {
                CostEstimate::create(array_merge($item, [
                    'project_id' => $project->id,
                    'facility_category_id' => $category?->id,
                    'approval_status' => 'Approved',
                    'is_completed' => false,
                ]));
            }

            return $project;
        });
    }
}
