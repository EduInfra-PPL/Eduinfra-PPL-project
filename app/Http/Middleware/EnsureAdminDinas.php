<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi rute hanya untuk persona Admin Dinas yang sudah terverifikasi.
 */
class EnsureAdminDinas
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && $user->is_verified && $user->role_enum === UserRole::AdminDinas->value,
            403,
            'Halaman ini hanya untuk Admin Dinas.',
        );

        return $next($request);
    }
}