<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Daftar seluruh akun. Status: all | pending | approved | rejected.
 */
class ListUsersAction
{
    public function execute(string $status = 'all', ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = User::query()
            ->leftJoin('regions', 'regions.id', '=', 'users.region_id')
            ->select('users.*', 'regions.name as region_name');

        match ($status) {
            'approved' => $query->where('users.is_verified', true),
            'pending' => $query->where('users.is_verified', false)->whereNull('users.rejected_at'),
            'rejected' => $query->where('users.is_verified', false)->whereNotNull('users.rejected_at'),
            default => null,
        };

        if ($search !== null && trim($search) !== '') {
            $term = '%'.mb_strtolower(trim($search)).'%';

            $query->where(function ($q) use ($term) {
                $q->whereRaw('lower(users.name) like ?', [$term])
                    ->orWhereRaw('lower(users.email) like ?', [$term])
                    ->orWhere('users.identity_number', 'like', $term);
            });
        }

        return $query->orderByRaw('users.last_login_at desc nulls last')
            ->orderByDesc('users.created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return array{all: int, approved: int, pending: int, rejected: int}
     */
    public function counts(): array
    {
        return [
            'all' => User::query()->count(),
            'approved' => User::query()->where('is_verified', true)->count(),
            'pending' => User::query()->where('is_verified', false)->whereNull('rejected_at')->count(),
            'rejected' => User::query()->where('is_verified', false)->whereNotNull('rejected_at')->count(),
        ];
    }
}