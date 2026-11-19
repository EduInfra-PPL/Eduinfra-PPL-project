<?php

namespace App\Http\Requests\Api\V1\Regions;

use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/regions/import - impor GeoJSON FeatureCollection (FR-02).
 * Hanya bentuk payload yang divalidasi di sini; tiap fitur divalidasi oleh ImportRegionsAction.
 */
class ImportRegionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware rute tulis (config eduinfra.write_middleware)
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:FeatureCollection'],
            'features' => ['required', 'array', 'min:1', 'max:500'],
        ];
    }
}
