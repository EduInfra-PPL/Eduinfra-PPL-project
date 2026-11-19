<?php

namespace App\Http\Requests\Api\V1\Schools;

use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** GET /api/v1/schools/nearby - sekolah dalam radius titik. */
class NearbySchoolsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // endpoint baca publik
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius_m' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'level' => ['nullable', Rule::in(School::LEVELS)],
        ];
    }
}
