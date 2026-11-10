<?php

namespace App\Services\ApiIndonesia;

use App\Services\Dapodik\SchoolSource;
use Generator;

/**
 * Sumber sekolah dari direktori API Indonesia. Hasilnya memakai kunci yang sama dengan
 * sumber CSV/Dapodik, sehingga diimpor lewat SchoolImporter (upsert per NPSN).
 */
class ApiIndonesiaSchoolSource implements SchoolSource
{
    public function __construct(
        private SchoolDirectoryClient $client,
        private SchoolDirectoryMapper $mapper,
        private array $filters = [],
    ) {}

    public function name(): string
    {
        return 'apiindonesia';
    }

    /** @return Generator<int, array<string, mixed>> */
    public function records(): Generator
    {
        foreach ($this->client->schools($this->filters) as $row) {
            ['record' => $record] = $this->mapper->toRecord($row);
            if ($record !== null) {
                yield $record;
            }
        }
    }
}
