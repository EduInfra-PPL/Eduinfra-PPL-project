<?php

namespace App\Http\Requests\Auth;

use App\Concerns\PasswordValidationRules;
use App\Enums\RegistrationRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi formulir registrasi pengguna baru (Domain 5).
 */
class RegisterUserRequest extends FormRequest
{
    use PasswordValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rapikan isian sebelum divalidasi: NIP/NIK hanya angka, email huruf kecil,
     * nomor seluler hanya angka dan tanda "+".
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'identity_number' => preg_replace('/\D/', '', (string) $this->input('identity_number')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'phone' => preg_replace('/[^\d+]/', '', (string) $this->input('phone')),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'registration_role' => ['required', Rule::enum(RegistrationRole::class)],
            'name' => ['required', 'string', 'max:255'],
            'identity_number' => [
                'required',
                'string',
                'regex:/^(\d{16}|\d{18})$/',
                Rule::unique(User::class, 'identity_number'),
            ],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)8\d{8,11}$/'],
            'region_id' => [
                'required',
                'integer',
                Rule::exists('regions', 'id')->where(
                    fn ($query) => $query->where('level', 'kab')->where('is_3t', true),
                ),
            ],
            'organization' => ['required', 'string', 'max:150'],
            'password' => $this->passwordRules(),
            'terms' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'registration_role.required' => 'Pilih peran & instansi penugasan Anda.',
            'registration_role.enum' => 'Peran yang dipilih tidak tersedia untuk registrasi mandiri.',
            'name.required' => 'Nama lengkap & gelar resmi wajib diisi.',
            'identity_number.required' => 'NIP (18 digit) atau NIK (16 digit) wajib diisi.',
            'identity_number.regex' => 'NIP harus 18 digit atau NIK harus 16 digit angka.',
            'identity_number.unique' => 'NIP/NIK ini sudah terdaftar di sistem.',
            'email.required' => 'Alamat email kedinasan wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            'phone.required' => 'Nomor seluler untuk token OTP wajib diisi.',
            'phone.regex' => 'Format nomor seluler tidak valid (contoh: 0812 4498 2100 atau +62 812 4498 2100).',
            'region_id.required' => 'Pilih kabupaten wilayah tugas koridor 3T.',
            'region_id.exists' => 'Wilayah yang dipilih bukan kabupaten koridor 3T yang terdaftar.',
            'organization.required' => 'Nama satdik / satuan kerja unit asal wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'terms.accepted' => 'Anda wajib menyetujui pakta integritas kerahasiaan data sarpras 3T.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'identity_number' => 'NIP/NIK',
            'email' => 'email',
            'phone' => 'nomor seluler',
            'region_id' => 'wilayah tugas',
            'organization' => 'satuan kerja',
            'password' => 'kata sandi',
        ];
    }
}