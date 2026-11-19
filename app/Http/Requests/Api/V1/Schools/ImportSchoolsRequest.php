<?php

namespace App\Http\Requests\Api\V1\Schools;

use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/schools/import - impor massal (FR-01, TR-08).
 * Hanya bentuk payload yang divalidasi di sini; tiap baris divalidasi oleh SchoolImporter.
 */
class ImportSchoolsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware rute tulis (config eduinfra.write_middleware)
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ['schools' => ['required', 'array', 'min:1', 'max:1000']];
    }
}
