<?php

namespace App\Http\Requests\Api\V1\Schools;

use App\Models\School;
use App\Services\Schools\SchoolImporter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * POST /api/v1/schools - daftarkan sekolah (FR-01).
 * Aturan sama dengan baris impor (SchoolImporter::profileRules), dengan jenjang wajib.
 */
class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware rute tulis (config eduinfra.write_middleware)
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(SchoolImporter $importer): array
    {
        return $importer->profileRules(['required', Rule::in(School::LEVELS)]);
    }
}
