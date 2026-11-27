<?php

namespace App\Http\Requests\Pendanaan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi unggahan Faktur Pajak susulan pada mutasi pencairan (`disbursements` — PBI-12 Update).
 */
class UpdateDisbursementTaxInvoiceRequest extends FormRequest
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
        return [
            'tax_invoice_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tax_invoice_file.required' => 'Berkas Faktur Pajak e-Faktur (PDF) wajib dipilih.',
            'tax_invoice_file.mimes' => 'Faktur Pajak e-Faktur harus berformat PDF.',
            'tax_invoice_file.max' => 'Ukuran berkas Faktur Pajak maksimal 10 MB.',
        ];
    }
}
