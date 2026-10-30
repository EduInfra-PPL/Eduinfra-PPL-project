<?php

namespace App\Actions\Reports;

use App\Models\DamageReport;
use Illuminate\Support\Facades\DB;

class CreateDamageReportAction
{
    /**
     * Eksekusi penyimpanan laporan kerusakan baru dengan nomor tiket unik.
     * Sesuai standar arsitektur AGENT_RULE.md: dibungkus DB::transaction.
     *
     * @param array<string, mixed> $data
     */
    public function execute(array $data): DamageReport
    {
        return DB::transaction(function () use ($data) {
            $year = date('Y');
            $ticketNum = $this->generateUniqueTicketNumber($year);

            return DamageReport::create([
                'ticket_num' => $ticketNum,
                'school_id' => $data['school_id'],
                'category_id' => $data['category_id'],
                'reporter_id' => auth()->id() ?? $data['reporter_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'],
                'severity_level' => (int) $data['severity_level'],
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Generate format nomor tiket standar: RPT-YYYY-XXXX (contoh: RPT-2026-0892).
     */
    protected function generateUniqueTicketNumber(string $year): string
    {
        do {
            $count = DamageReport::whereYear('created_at', $year)->count() + 1;
            $randomSuffix = str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            $candidate = "RPT-{$year}-{$randomSuffix}";

            // Jika ada bentrok kode unik, tambahkan digit acak
            if (DamageReport::where('ticket_num', $candidate)->exists()) {
                $candidate = "RPT-{$year}-" . strtoupper(bin2hex(random_bytes(2)));
            }
        } while (DamageReport::where('ticket_num', $candidate)->exists());

        return $candidate;
    }
}

