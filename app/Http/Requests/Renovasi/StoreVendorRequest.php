<?php

namespace App\Http\Requests\Renovasi;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $grade = strtoupper((string) ($this->input('grade') ?: 'K3'));
        $defaultMaxLimit = match ($grade) {
            'K1' => 1_000_000_000,
            'K2' => 1_500_000_000,
            'K3' => 2_500_000_000,
            'M1' => 10_000_000_000,
            'M2' => 25_000_000_000,
            'B1', 'B2' => 50_000_000_000,
            default => 2_500_000_000,
        };

        $this->merge([
            'entity_type' => strtoupper((string) ($this->input('entity_type') ?: 'CV')),
            'grade' => $grade,
            'max_contract_limit' => $this->input('max_contract_limit') ?: $defaultMaxLimit,
            'rating' => $this->input('rating') !== null && $this->input('rating') !== ''
                ? (float) $this->input('rating')
                : 4.80,
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
            'sbu_status' => $this->input('sbu_status') ?: 'verified',
        ]);
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:200'],
            'entity_type' => ['required', 'string', 'max:30'],
            'license_num' => ['required', 'string', 'max:100', 'unique:vendors,license_num'],
            'nib' => ['nullable', 'string', 'max:50'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'service_region_id' => ['required', 'integer', 'exists:regions,id'],
            'grade' => ['required', 'in:K1,K2,K3,M1,M2,B1,B2'],
            'qualification_label' => ['nullable', 'string', 'max:150'],
            'max_contract_limit' => ['required', 'numeric', 'min:10000000'],
            'sbu_status' => ['required', 'in:verified,review'],
            'sbu_valid_until' => ['nullable', 'date'],
            'specialty' => ['nullable', 'string', 'max:150'],
            'logistics_note' => ['nullable', 'string', 'max:255'],
            'lead_engineer' => ['nullable', 'string', 'max:150'],
            'engineer_role' => ['nullable', 'string', 'max:150'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'is_active' => ['required', 'boolean'],
            'assign_project_id' => ['nullable', 'integer', 'exists:repair_projects,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Nama badan usaha kontraktor wajib diisi.',
            'license_num.required' => 'Nomor registrasi SBU / LPJK wajib diisi.',
            'license_num.unique' => 'Nomor registrasi SBU / LPJK sudah terdaftar pada rekanan lain.',
            'contact_phone.required' => 'Nomor telepon kontak rekanan wajib diisi.',
            'service_region_id.required' => 'Wilayah jangkauan layanan (Master Wilayah) wajib dipilih.',
            'rating.max' => 'Skor rating maksimal 5.00.',
        ];
    }
}
