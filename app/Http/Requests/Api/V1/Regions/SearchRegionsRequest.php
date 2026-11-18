<?php

namespace App\Http\Requests\Api\V1\Regions;

use App\Enums\ThreeTCategory;
use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** GET /api/v1/regions - daftar wilayah tanpa geometri. */
class SearchRegionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // endpoint baca publik
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', Rule::in(Region::LEVELS)],
            'code_prefix' => ['nullable', 'string', 'max:20'],
            'is_3t' => ['nullable', 'in:true,false,1,0'],
            'category' => ['nullable', Rule::in(ThreeTCategory::values())],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    /**
     * Filter siap pakai untuk RegionQueryService::paginate().
     *
     * @return array{q: ?string, level: ?string, code_prefix: ?string, is_3t: ?bool, category: ?string}
     */
    public function filters(): array
    {
        $v = $this->validated();

        return [
            'q' => $v['q'] ?? null,
            'level' => $v['level'] ?? null,
            'code_prefix' => $v['code_prefix'] ?? null,
            'is_3t' => filled($v['is_3t'] ?? null) ? filter_var($v['is_3t'], FILTER_VALIDATE_BOOLEAN) : null,
            'category' => $v['category'] ?? null,
        ];
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? 100);
    }
}
