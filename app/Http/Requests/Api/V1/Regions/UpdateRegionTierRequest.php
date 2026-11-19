<?php

namespace App\Http\Requests\Api\V1\Regions;

use Illuminate\Foundation\Http\FormRequest;

/** PATCH /api/v1/regions/{id}/tier - tingkat prioritas 3T (0 = bukan 3T, 1 = Tier-1, 2 = Tier-2). */
class UpdateRegionTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware rute tulis (config eduinfra.write_middleware)
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ['is_3t_tier' => ['required', 'integer', 'in:0,1,2']];
    }
}
