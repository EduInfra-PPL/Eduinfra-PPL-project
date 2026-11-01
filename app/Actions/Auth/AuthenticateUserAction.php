<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;

/**
 * Autentikasi login (PBI-13): NIP/NIK atau email kedinasan + kata sandi.
 * Dipanggil oleh Fortify::authenticateUsing() di FortifyServiceProvider.
 */
class AuthenticateUserAction
{
    /** Masa "ingat sesi terpercaya": maksimal 8 jam (protokol BSSN). */
    public const REMEMBER_MINUTES = 480;

    public function __construct(private readonly LoginRateLimiter $limiter) {}

    /**
     * @throws ValidationException
     */
    public function execute(Request $request): User
    {
        $login = (string) $request->input(Fortify::username());
        $password = (string) $request->input('password');

        $user = User::query()
            ->where('email', $login)
            ->orWhere('identity_number', $login)
            ->first();

        if ($user === null || ! Hash::check($password, $user->password)) {
            // Menghitung percobaan gagal untuk pembatasan (throttle) login.
            $this->limiter->increment($request);

            throw ValidationException::withMessages([
                Fortify::username() => 'NIP/email atau kata sandi tidak sesuai dengan data kedinasan kami.',
            ]);
        }

        if (! $user->is_verified) {
            throw ValidationException::withMessages([
                Fortify::username() => 'Akun Anda belum diverifikasi administrator. Hubungi Helpdesk Pusdatin 1500-087.',
            ]);
        }

        $this->limitRememberDuration();

        return $user;
    }

    private function limitRememberDuration(): void
    {
        $guard = Auth::guard(config('fortify.guard'));

        if (method_exists($guard, 'setRememberDuration')) {
            $guard->setRememberDuration(self::REMEMBER_MINUTES);
        }
    }
}
