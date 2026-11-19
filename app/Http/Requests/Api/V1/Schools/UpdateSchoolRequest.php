<?php

namespace App\Http\Requests\Api\V1\Schools;

use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * PUT /api/v1/schools/{id} - ubah kontak dan akreditasi (Tabel 16: U = perubahan kontak/akreditasi).
 * Koreksi nama/koordinat/wilayah dilakukan lewat impor (upsert berdasarkan NPSN).
 */
class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware rute tulis (config eduinfra.write_middleware)
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'accreditation' => ['sometimes', 'nullable', Rule::in(School::ACCREDITATIONS)],
            'contact_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'contact_email' => ['sometimes', 'nullable', 'email', 'max:150'],
            // field di bawah ini tidak boleh diubah lewat endpoint ini
            'npsn' => ['prohibited'],
            'name' => ['prohibited'],
            'lat' => ['prohibited'],
            'lng' => ['prohibited'],
            'region_id' => ['prohibited'],
        ];
    }
}
