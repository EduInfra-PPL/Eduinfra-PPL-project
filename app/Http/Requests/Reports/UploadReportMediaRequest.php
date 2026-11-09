<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;

class UploadReportMediaRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan mengunggah bukti foto.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi berkas media bukti fisik kerusakan (Domain 2 / PBI-05).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:10240', // Maksimal 10 MB per berkas foto
            ],
        ];
    }

    /**
     * Pesan kesalahan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Berkas foto bukti kerusakan fisik wajib dipilih.',
            'file.file' => 'Unggahan harus berupa berkas yang valid.',
            'file.image' => 'Berkas yang diunggah harus berupa gambar/foto.',
            'file.mimes' => 'Format foto yang diizinkan hanya JPEG, JPG, PNG, atau WebP.',
            'file.max' => 'Ukuran berkas foto maksimal adalah 10 MB.',
        ];
    }
}
