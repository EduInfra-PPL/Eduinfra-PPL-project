<?php

namespace App\Http\Requests\Renovasi;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $reportId = $this->input('report_id') ?: $this->input('damage_report_id');
        $targetStart = $this->input('target_start') ?: $this->input('start_date');
        $targetEnd = $this->input('target_end') ?: $this->input('target_completion_date');

        $this->merge([
            'report_id' => $reportId ?: null,
            'target_start' => $targetStart ?: now()->toDateString(),
            'target_end' => $targetEnd ?: now()->addDays(120)->toDateString(),
        ]);
    }

    public function rules(): array
    {
        return [
            'report_id' => ['nullable', 'integer', 'exists:damage_reports,id'],
            'school_id' => ['required_without:report_id', 'nullable', 'integer', 'exists:schools,id'],
            'facility_category_id' => ['nullable', 'integer', 'exists:facility_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'budget_allocated' => ['required', 'numeric', 'min:1000000'],
            'contractor_name' => ['nullable', 'string', 'max:200'],
            'consortium_name' => ['nullable', 'string', 'max:200'],
            'surveyor_name' => ['nullable', 'string', 'max:150'],
            'priority' => ['required', 'in:critical,high,medium,low'],
            'target_start' => ['nullable', 'date'],
            'target_end' => ['nullable', 'date', 'after_or_equal:target_start'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required_without' => 'Pilih sekolah sasaran dari Master Data atau hubungkan ke tiket laporan kerusakan.',
            'title.required' => 'Nama atau lingkup proyek renovasi wajib diisi.',
            'budget_allocated.required' => 'Pagu alokasi anggaran (RAB) wajib diisi.',
            'budget_allocated.min' => 'Pagu alokasi anggaran minimal Rp 1.000.000.',
            'target_end.after_or_equal' => 'Target selesai fisik tidak boleh lebih awal dari tanggal mulai SPK.',
        ];
    }
}
