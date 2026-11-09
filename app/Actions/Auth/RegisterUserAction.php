<?php

namespace App\Actions\Auth;

use App\Enums\RegistrationRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Membuat permohonan akun baru. Akun berstatus belum terverifikasi (is_verified = false)
 * sampai administrator menyetujuinya, sehingga belum dapat login.
 */
class RegisterUserAction
{
    /**
     * @param  array<string, mixed>  $data  data tervalidasi RegisterUserRequest
     */
    public function execute(array $data): User
    {
        $role = RegistrationRole::from($data['registration_role']);

        return DB::transaction(function () use ($data, $role): User {
            // forceFill: atribut #[Fillable] pada model User belum dikenali Laravel 11.
            // Event Registered sengaja tidak dipicu: pengiriman email verifikasi
            // (MustVerifyEmail) dinonaktifkan pada config/fortify.php.
            $user = new User;

            $user->forceFill([
                'name' => trim($data['name']),
                'email' => $data['email'],
                'password' => $data['password'],
                'identity_number' => $data['identity_number'],
                'phone' => $this->normalizePhone($data['phone']),
                'registration_role' => $role->value,
                'role_enum' => $role->persona()->value,
                'organization' => trim($data['organization']),
                'region_id' => (int) $data['region_id'],
                'is_verified' => false,
                'terms_accepted_at' => now(),
            ])->save();

            return $user;
        });
    }

    /**
     * Seragamkan nomor seluler ke format +62...
     */
    private function normalizePhone(string $phone): string
    {
        return preg_replace('/^(?:\+?62|0)/', '+62', $phone);
    }
}