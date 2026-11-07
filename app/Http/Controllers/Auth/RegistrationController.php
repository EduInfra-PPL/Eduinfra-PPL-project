<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ListRegistrationRegionsAction;
use App\Actions\Auth\RegisterUserAction;
use App\Enums\RegistrationRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller registrasi akun (Domain 5). Strict Thin Controller (AGENT_RULE.md).
 */
class RegistrationController extends Controller
{
    /**
     * Tampilkan formulir registrasi pengguna baru.
     */
    public function create(ListRegistrationRegionsAction $regions): View
    {
        return view('auth.register', [
            'roles' => RegistrationRole::cases(),
            'regions' => $regions->execute(),
        ]);
    }

    /**
     * Simpan permohonan registrasi, lalu arahkan ke halaman login.
     */
    public function store(RegisterUserRequest $request, RegisterUserAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('login')->with(
            'status',
            'Permohonan registrasi akun berhasil dikirim. Administrator akan memverifikasi data Anda (SLA < 24 jam) sebelum akses diterbitkan.',
        );
    }
}