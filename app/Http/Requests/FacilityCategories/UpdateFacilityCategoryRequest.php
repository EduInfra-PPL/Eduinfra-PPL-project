<?php

namespace App\Http\Requests\FacilityCategories;

use App\Models\FacilityCategory;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Ubah bobot urgensi, satuan bawaan, atau status aktif kategori (FR-03).
 * Nama dan sub-kategori tidak boleh diubah karena sudah dirujuk laporan.
 */
class UpdateFacilityCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->default_unit)) {
            $this->merge(['default_unit' => trim($this->default_unit)]);
        }
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'urgency_weight' => ['sometimes', 'required', 'integer', 'between:'.FacilityCategory::MIN_WEIGHT.','.FacilityCategory::MAX_WEIGHT],
            'default_unit' => ['sometimes', 'nullable', 'string', 'max:30'],
            'is_enabled' => ['sometimes', 'required', 'boolean'],
            'name' => ['prohibited'],
            'sub_category' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'urgency_weight.required' => 'Pilih bobot urgensi.',
            'urgency_weight.integer' => 'Bobot urgensi harus berupa angka bulat.',
            'urgency_weight.between' => 'Bobot urgensi harus antara 1 sampai 5.',
            'default_unit.max' => 'Satuan maksimal 30 karakter.',
            'name.prohibited' => 'Nama kategori tidak dapat diubah.',
            'sub_category.prohibited' => 'Sub-kategori tidak dapat diubah.',
        ];
    }
}
