<?php

namespace App\Http\Requests\Api\V1\Regions;

use Illuminate\Foundation\Http\FormRequest;

/** GET /api/v1/regions/lookup?lat=&lng= - wilayah yang memuat titik. */
class LookupRegionRequest extends FormRequest
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
        ];
    }
}
