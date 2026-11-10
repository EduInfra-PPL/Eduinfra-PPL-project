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
    /** @param  list<string>  $provinceCodes  kode provinsi (mis. ['32']); kosong = seluruh Indonesia */
    public function __construct(
        private SchoolDirectoryClient $client,
        private SchoolDirectoryMapper $mapper,
        private array $provinceCodes = [],
    ) {}

    public function name(): string
    {
        return 'apiindonesia'.($this->provinceCodes === [] ? '' : ' provinsi '.implode(',', $this->provinceCodes));
    }

    /** @return Generator<int, array<string, mixed>> */
    public function records(): Generator
    {
        // parameter filter provinsi di API adalah provinsi_id (province_id diabaikan API)
        $filterSets = $this->provinceCodes === []
            ? [[]]
            : array_map(fn (string $code): array => ['provinsi_id' => $code], $this->provinceCodes);

        foreach ($filterSets as $filters) {
            foreach ($this->client->schools($filters) as $row) {
                ['record' => $record] = $this->mapper->toRecord($row);
                if ($record !== null) {
                    yield $record;
                }
            }
        }
    }
}
