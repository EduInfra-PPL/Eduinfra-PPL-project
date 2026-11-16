<?php

namespace App\Http\Requests\Renovasi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'progress_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:pending,in_progress,completed,delayed'],
            'surveyor_name' => ['nullable', 'string', 'max:150'],
            'assessment_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'proof_photo' => ['nullable', 'image', 'max:10240'],
            'completed_estimates' => ['nullable', 'array'],
            'completed_estimates.*' => ['integer', 'exists:cost_estimates,id'],
            'sync_estimates' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'progress_pct.required' => 'Persentase kemajuan fisik wajib diisi.',
            'progress_pct.between' => 'Persentase kemajuan fisik harus berada di rentang 0% hingga 100%.',
            'status.in' => 'Status milestone tidak valid.',
            'proof_photo.image' => 'Bukti lapangan harus berupa berkas gambar (JPG, PNG, atau WEBP).',
        ];
    }
}
