<?php

namespace App\Http\Requests\Pendanaan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi payload pencatatan pencairan dana termin ke vendor (`disbursements` — PBI-12).
 */
class StoreDisbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isDraft = $this->input('submit_mode') === 'draft';

        return [
            'project_id' => ['required', 'integer', 'exists:repair_projects,id'],
            'stage_num' => ['required', 'integer', 'in:1,2,3'],
            'submit_mode' => ['nullable', 'string', 'in:disbursed,draft'],
            'receipt_file' => [
                Rule::requiredIf(! $isDraft),
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
            'tax_invoice_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'otp_code' => [
                Rule::requiredIf(! $isDraft),
                'nullable',
                'string',
                'regex:/^\d{6}$/',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Proyek sasaran pencairan wajib dipilih.',
            'project_id.exists' => 'Proyek renovasi sasaran tidak ditemukan.',
            'stage_num.required' => 'Tahap termin pencairan wajib dipilih.',
            'stage_num.in' => 'Tahap termin hanya boleh bernilai Termin 1, 2, atau 3.',
            'receipt_file.required' => 'Dokumen kuitansi/BAP bertanda tangan (PDF) wajib diunggah.',
            'receipt_file.mimes' => 'Dokumen kuitansi/BAP harus berformat PDF.',
            'receipt_file.max' => 'Ukuran dokumen kuitansi/BAP maksimal 10 MB.',
            'tax_invoice_file.mimes' => 'Faktur pajak e-Faktur harus berformat PDF.',
            'tax_invoice_file.max' => 'Ukuran berkas faktur pajak maksimal 10 MB.',
            'otp_code.required' => 'Kode Dual-Token OTP Escrow (6 digit) wajib diisi untuk eksekusi transfer.',
            'otp_code.regex' => 'Kode Dual-Token OTP Escrow harus terdiri dari tepat 6 digit angka.',
        ];
    }
}
