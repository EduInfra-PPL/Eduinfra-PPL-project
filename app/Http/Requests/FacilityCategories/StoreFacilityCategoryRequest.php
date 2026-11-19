<?php

namespace App\Http\Requests\FacilityCategories;

use App\Models\FacilityCategory;
use Illuminate\Foundation\Http\FormRequest;

/** Tambah kategori/sub-kategori fasilitas beserta bobot urgensi 1-5 (FR-03). */
class StoreFacilityCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware rute (auth + Admin Dinas)
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'sub_category' => is_string($this->sub_category) ? trim($this->sub_category) : $this->sub_category,
            'default_unit' => is_string($this->default_unit) ? trim($this->default_unit) : $this->default_unit,
        ]);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'urgency_weight' => ['required', 'integer', 'between:'.FacilityCategory::MIN_WEIGHT.','.FacilityCategory::MAX_WEIGHT],
            'default_unit' => ['nullable', 'string', 'max:30'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
            'sub_category.max' => 'Sub-kategori maksimal 100 karakter.',
            'urgency_weight.required' => 'Pilih bobot urgensi.',
            'urgency_weight.integer' => 'Bobot urgensi harus berupa angka bulat.',
            'urgency_weight.between' => 'Bobot urgensi harus antara 1 sampai 5.',
            'default_unit.max' => 'Satuan maksimal 30 karakter.',
        ];
    }
}
