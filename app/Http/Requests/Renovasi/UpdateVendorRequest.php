<?php

namespace App\Http\Requests\Renovasi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('grade')) {
            $this->merge(['grade' => strtoupper((string) $this->input('grade'))]);
        }
        if ($this->has('entity_type')) {
            $this->merge(['entity_type' => strtoupper((string) $this->input('entity_type'))]);
        }
        if ($this->has('is_active')) {
            $this->merge(['is_active' => $this->boolean('is_active')]);
        }
    }

    public function rules(): array
    {
        $vendorParam = $this->route('vendor');
        $vendorId = is_object($vendorParam) ? $vendorParam->id : (int) $vendorParam;

        return [
            'company_name' => ['required', 'string', 'max:200'],
            'entity_type' => ['required', 'string', 'max:30'],
            'license_num' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vendors', 'license_num')->ignore($vendorId),
            ],
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
            'license_num.unique' => 'Nomor registrasi SBU / LPJK sudah digunakan oleh rekanan lain.',
            'contact_phone.required' => 'Nomor telepon kontak rekanan wajib diisi.',
            'service_region_id.required' => 'Wilayah jangkauan layanan wajib dipilih.',
        ];
    }
}
