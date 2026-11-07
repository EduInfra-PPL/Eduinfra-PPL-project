<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

/**
 * Mencatat waktu dan jumlah login setiap kali pengguna berhasil masuk.
 */
class RecordUserLogin
{
    public function handle(Login $event): void
    {
        User::query()
            ->whereKey($event->user->getAuthIdentifier())
            ->update([
                'last_login_at' => now(),
                'login_count' => DB::raw('login_count + 1'),
            ]);
    }
}