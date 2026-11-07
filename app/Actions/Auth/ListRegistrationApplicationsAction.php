<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Daftar permohonan registrasi: 'pending' (menunggu) atau 'rejected' (ditolak).
 */
class ListRegistrationApplicationsAction
{
    public function execute(string $status = 'pending', int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()
            ->leftJoin('regions', 'regions.id', '=', 'users.region_id')
            ->select('users.*', 'regions.name as region_name')
            ->whereNotNull('users.registration_role')
            ->where('users.is_verified', false);

        $status === 'rejected'
            ? $query->whereNotNull('users.rejected_at')->latest('users.rejected_at')
            : $query->whereNull('users.rejected_at')->latest('users.created_at');

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @return array{pending: int, rejected: int}
     */
    public function counts(): array
    {
        $base = fn () => User::query()->whereNotNull('registration_role')->where('is_verified', false);

        return [
            'pending' => $base()->whereNull('rejected_at')->count(),
            'rejected' => $base()->whereNotNull('rejected_at')->count(),
        ];
    }
}