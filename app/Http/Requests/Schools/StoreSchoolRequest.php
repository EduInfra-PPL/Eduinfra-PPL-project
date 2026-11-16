<?php

namespace App\Http\Requests\Schools;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Pendaftaran sekolah baru oleh Admin Dinas (FR-01). */
class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware rute (auth + EnsureAdminDinas)
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'npsn' => ['required', 'regex:/^[0-9]{8}$/', 'unique:schools,npsn'],
            'name' => ['required', 'string', 'max:200'],
            'educational_level' => ['required', Rule::in(['SD', 'SMP', 'SMA'])],
            'address' => ['nullable', 'string', 'max:1000'],
            'lat' => ['required', 'numeric', 'between:-11.5,6.5'],
            'lng' => ['required', 'numeric', 'between:94.5,141.5'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'accreditation' => ['nullable', Rule::in(['A', 'B', 'C'])],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:150'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'npsn.required' => 'NPSN wajib diisi.',
            'npsn.regex' => 'NPSN harus 8 digit angka.',
            'npsn.unique' => 'NPSN sudah terdaftar.',
            'name.required' => 'Nama sekolah wajib diisi.',
            'educational_level.required' => 'Pilih jenjang sekolah.',
            'lat.required' => 'Koordinat lintang wajib diisi.',
            'lat.between' => 'Lintang harus berada di wilayah Indonesia.',
            'lng.required' => 'Koordinat bujur wajib diisi.',
            'lng.between' => 'Bujur harus berada di wilayah Indonesia.',
            'contact_email.email' => 'Format email tidak valid.',
        ];
    }
}
