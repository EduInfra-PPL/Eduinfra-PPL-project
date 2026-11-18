<?php

namespace App\Http\Requests\Api\V1\Regions;

use App\Enums\ThreeTCategory;
use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** GET /api/v1/regions/geojson - FeatureCollection wilayah untuk peta/choropleth. */
class RegionsGeoJsonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // endpoint baca publik
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'level' => ['nullable', Rule::in(Region::LEVELS)],
            'code_prefix' => ['nullable', 'string', 'max:20'],
            'is_3t' => ['nullable', 'in:true,false,1,0'],
            'category' => ['nullable', Rule::in(ThreeTCategory::values())],
            'simplify' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ];
    }

    /**
     * Filter siap pakai untuk RegionQueryService::geoJson().
     *
     * @return array{level: ?string, code_prefix: ?string, is_3t: ?bool, category: ?string}
     */
    public function filters(): array
    {
        $v = $this->validated();

        return [
            'level' => $v['level'] ?? null,
            'code_prefix' => $v['code_prefix'] ?? null,
            'is_3t' => filled($v['is_3t'] ?? null) ? filter_var($v['is_3t'], FILTER_VALIDATE_BOOLEAN) : null,
            'category' => $v['category'] ?? null,
        ];
    }

    /** Toleransi penyederhanaan poligon dalam derajat (default 0.001; 0 = tanpa penyederhanaan). */
    public function simplify(): float
    {
        return (float) ($this->validated('simplify') ?? 0.001);
    }
}
