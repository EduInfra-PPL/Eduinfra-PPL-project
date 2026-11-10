<?php

namespace App\Http\Requests\Pendanaan;

use App\Services\Pendanaan\VerifiedProjectLookupService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAidCampaignRequest extends FormRequest
{
    /**
     * Otorisasi pembukaan kampanye bantuan CSR (Domain 4).
     * Mengacu pada PRD Tabel Persona: Admin Dinas / Koordinator.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi penyimpanan kampanye CSR baru (`aid_campaigns`).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(VerifiedProjectLookupService $projectLookup): array
    {
        return [
            'project_id' => [
                'required',
                'integer',
                Rule::unique('aid_campaigns', 'project_id'),
                function (string $attribute, mixed $value, Closure $fail) use ($projectLookup): void {
                    $project = $projectLookup->findById((int) $value);

                    if ($project === null || ($project['rab_amount'] ?? 0) <= 0) {
                        $fail('Proyek sasaran belum terverifikasi atau belum memiliki RAB SNI yang disetujui.');
                    }
                },
            ],
            'title' => ['required', 'string', 'min:10', 'max:200'],
            'deadline' => ['required', 'date', 'after:today'],
        ];
    }

    /**
     * Pesan kesalahan validasi berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Proyek sasaran terverifikasi wajib dipilih.',
            'project_id.integer' => 'Format identitas proyek tidak valid.',
            'project_id.unique' => 'Proyek ini sudah memiliki program / kampanye CSR aktif.',
            'title.required' => 'Judul kampanye / program bantuan wajib diisi.',
            'title.min' => 'Judul kampanye minimal 10 karakter.',
            'title.max' => 'Judul kampanye maksimal 200 karakter.',
            'deadline.required' => 'Batas waktu penggalangan (deadline) wajib ditentukan.',
            'deadline.date' => 'Format batas waktu penggalangan tidak valid.',
            'deadline.after' => 'Batas waktu penggalangan harus lebih dari hari ini.',
        ];
    }
}
