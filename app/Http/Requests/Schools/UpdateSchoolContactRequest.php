<?php

namespace App\Http\Requests\Schools;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Ubah kontak dan akreditasi sekolah (FR-01). Nama, NPSN, dan koordinat tidak bisa diubah di sini. */
class UpdateSchoolContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'accreditation' => ['nullable', Rule::in(['A', 'B', 'C'])],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:150'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'contact_email.email' => 'Format email tidak valid.',
        ];
    }
}
