<?php

namespace App\Http\Requests\Pendanaan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi payload penyaluran komitmen CSR donatur (`aid_contributions` — PBI-11).
 */
class StoreAidContributionRequest extends FormRequest
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
            'campaign_id' => ['required', 'integer', 'exists:aid_campaigns,id'],
            'donor_name' => ['required', 'string', 'max:150'],
            'donor_unit' => ['nullable', 'string', 'max:150'],
            'contribution_type' => ['required', 'string', 'in:cash,goods'],
            'amount_or_goods' => ['required', 'integer', 'min:100000'],
            'payment_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'campaign_id.required' => 'Kampanye bantuan CSR tujuan wajib dipilih.',
            'campaign_id.exists' => 'Kampanye bantuan CSR tidak ditemukan.',
            'donor_name.required' => 'Nama entitas mitra CSR atau donatur wajib diisi.',
            'contribution_type.in' => 'Bentuk komitmen harus berupa Dana Tunai (cash) atau Material/Barang (goods).',
            'amount_or_goods.required' => 'Nominal komitmen bantuan wajib diisi.',
            'amount_or_goods.min' => 'Minimal komitmen bantuan adalah Rp 100.000.',
            'payment_proof.mimes' => 'Bukti dokumen harus berformat PDF, JPG, atau PNG.',
            'payment_proof.max' => 'Ukuran berkas bukti maksimal 5 MB.',
        ];
    }
}
