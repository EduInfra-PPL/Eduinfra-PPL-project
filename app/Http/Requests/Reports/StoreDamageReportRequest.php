<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;

class StoreDamageReportRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki otorisasi untuk membuat laporan.
     * Pelapor publik dan guru diizinkan secara terbuka (Tabel 15 PRD).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk penyimpanan laporan kerusakan baru (PBI-04).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'category_id' => ['required', 'integer', 'exists:facility_categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'severity_level' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }

    /**
     * Pesan kustom kesalahan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'school_id.required' => 'Satuan pendidikan (sekolah) wajib dipilih.',
            'school_id.exists' => 'Sekolah yang dipilih tidak valid atau belum terdaftar.',
            'category_id.required' => 'Kategori komponen fasilitas wajib dipilih.',
            'category_id.exists' => 'Kategori fasilitas yang dipilih tidak valid.',
            'title.required' => 'Judul indikasi kerusakan wajib diisi.',
            'title.max' => 'Judul indikasi kerusakan maksimal 200 karakter.',
            'description.required' => 'Deskripsi detail kerusakan fisik wajib diisi.',
            'description.min' => 'Deskripsi detail kerusakan minimal 10 karakter.',
            'description.max' => 'Deskripsi detail kerusakan maksimal 1000 karakter.',
            'severity_level.required' => 'Tingkat urgensi kerusakan (skala 1–5) wajib dipilih.',
            'severity_level.between' => 'Tingkat urgensi kerusakan harus berada di antara skala 1 hingga 5.',
        ];
    }
}

