<?php

namespace App\Services\ApiIndonesia;

use App\Services\Dapodik\SchoolSource;
use Generator;

/**
 * Sumber sekolah dari direktori API Indonesia. Setiap himpunan filter (mis. satu kabupaten)
 * diambil terpisah agar tidak kena batas offset 5.000 per kueri.
 * Hasilnya memakai kunci yang sama dengan sumber lain.
 */
class ApiIndonesiaSchoolSource implements SchoolSource
{
    /** @param  list<array<string, string>>  $filterSets  mis. [['kabupaten_id' => '3209'], ...]; [] = satu kueri tanpa filter */
    public function __construct(
        private SchoolDirectoryClient $client,
        private SchoolDirectoryMapper $mapper,
        private array $filterSets = [],
    ) {}

    public function name(): string
    {
        return 'apiindonesia ('.count($this->filterSets ?: [[]]).' kueri)';
    }

    /** @return Generator<int, array<string, mixed>> */
    public function records(): Generator
    {
        foreach ($this->filterSets ?: [[]] as $filters) {
            foreach ($this->client->schools($filters) as $row) {
                ['record' => $record] = $this->mapper->toRecord($row);
                if ($record !== null) {
                    yield $record;
                }
            }
        }
    }
}
