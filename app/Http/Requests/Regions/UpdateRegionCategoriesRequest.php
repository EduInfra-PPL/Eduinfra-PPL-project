<?php

namespace App\Http\Requests\Regions;

use Illuminate\Foundation\Http\FormRequest;

/** PATCH /regions/{id}/categories - minimal satu kategori 3T harus dikirim. */
class UpdateRegionCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware rute tulis (config eduinfra.write_middleware)
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            // tanpa 'sometimes': aturan required_without_all harus tetap dievaluasi saat field absen
            'is_tertinggal' => ['boolean', 'required_without_all:is_terdepan,is_terluar'],
            'is_terdepan' => ['boolean', 'required_without_all:is_tertinggal,is_terluar'],
            'is_terluar' => ['boolean', 'required_without_all:is_tertinggal,is_terdepan'],
        ];
    }
}
