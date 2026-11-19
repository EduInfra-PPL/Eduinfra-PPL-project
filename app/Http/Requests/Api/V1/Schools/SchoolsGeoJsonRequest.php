<?php

namespace App\Http\Requests\Api\V1\Schools;

use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** GET /api/v1/schools/geojson - FeatureCollection sekolah untuk peta. */
class SchoolsGeoJsonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // endpoint baca publik
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'bbox' => ['nullable', 'string', 'regex:/^-?\d+(\.\d+)?(,-?\d+(\.\d+)?){3}$/'],
            'level' => ['nullable', Rule::in(School::LEVELS)],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50000'],
        ];
    }
}
