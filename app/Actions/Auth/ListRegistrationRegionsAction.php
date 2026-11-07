<?php

namespace App\Actions\Auth;

use App\Models\Region;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Daftar pilihan "Provinsi & Kabupaten Koridor 3T" untuk form registrasi:
 * hanya kabupaten dengan is_3t_tier > 0, diberi label "Nama Kabupaten, Nama Provinsi".
 */
class ListRegistrationRegionsAction
{
    /**
     * @return Collection<int, array{id: int, label: string}>
     */
    public function execute(): Collection
    {
        $provinces = Region::query()
            ->select(['code', 'name'])
            ->where('level', 'prov')
            ->pluck('name', 'code');

        return Region::query()
            ->select(Region::COLUMNS)
            ->where('level', 'kab')
            ->where('is_3t_tier', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(fn (Region $region): array => [
                'id' => $region->id,
                'label' => $region->name.', '.($provinces[Str::before($region->code, '.')] ?? 'Indonesia'),
            ]);
    }
}