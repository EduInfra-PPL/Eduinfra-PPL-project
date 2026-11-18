<?php

namespace App\Http\Requests\Api\V1\Schools;

use App\Enums\ThreeTCategory;
use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** GET /api/v1/schools - cari (NPSN/nama), filter jenjang/wilayah/3T/kategori, paginasi (FR-17). */
class SearchSchoolsRequest extends FormRequest
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
            'level' => ['nullable', Rule::in(School::LEVELS)],
            'region_id' => ['nullable', 'integer', 'min:1'],
            'region_code' => ['nullable', 'string', 'max:20'],
            'is_3t' => ['nullable', 'in:true,false,1,0'],
            'category' => ['nullable', Rule::in(ThreeTCategory::values())],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Filter siap pakai untuk SchoolQueryService::applyFilters().
     *
     * @return array{q: ?string, level: ?string, region_id: ?int, region_code: ?string, is_3t: ?bool, category: ?string}
     */
    public function filters(): array
    {
        $v = $this->validated();

        return [
            'q' => $v['q'] ?? null,
            'level' => $v['level'] ?? null,
            'region_id' => filled($v['region_id'] ?? null) ? (int) $v['region_id'] : null,
            'region_code' => $v['region_code'] ?? null,
            'is_3t' => filled($v['is_3t'] ?? null) ? filter_var($v['is_3t'], FILTER_VALIDATE_BOOLEAN) : null,
            'category' => $v['category'] ?? null,
        ];
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? 20);
    }
}
