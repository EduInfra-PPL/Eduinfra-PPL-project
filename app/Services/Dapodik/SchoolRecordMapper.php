<?php

namespace App\Services\Dapodik;

/**
 * Memetakan baris mentah dari sumber eksternal ke kunci internal sistem.
 *
 * $fieldMap: kunci internal => nama kolom/field di sumber, mis. ['npsn' => 'NPSN'].
 * Nama kolom Dapodik resmi belum diketahui, jadi peta ini dikonfigurasi di config/dapodik.php.
 */
class SchoolRecordMapper
{
    /** @param array<string, string> $fieldMap */
    public function __construct(private array $fieldMap) {}

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public function map(array $raw): array
    {
        $record = [];
        foreach ($this->fieldMap as $internal => $source) {
            $value = $raw[$source] ?? null;
            if (is_string($value)) {
                $value = trim($value);
                $value = $value === '' ? null : $value;
            }
            $record[$internal] = $value;
        }

        return $record;
    }
}
