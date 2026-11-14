<?php

namespace App\Services\Dapodik;

use Generator;
use RuntimeException;

/**
 * Membaca berkas CSV hasil ekspor Dapodik (baris pertama = header).
 * Berkas dibaca baris demi baris (streaming) sehingga aman untuk ukuran besar.
 */
class CsvSchoolSource implements SchoolSource
{
    private const UTF8_BOM = "\xEF\xBB\xBF";

    public function __construct(
        private string $path,
        private SchoolRecordMapper $mapper,
        private string $delimiter = ',',
    ) {}

    public function name(): string
    {
        return 'csv:'.$this->path;
    }

    /** @return Generator<int, array<string, mixed>> */
    public function records(): Generator
    {
        if (! is_file($this->path) || ! is_readable($this->path)) {
            throw new RuntimeException("Berkas CSV tidak dapat dibaca: {$this->path}");
        }

        $handle = fopen($this->path, 'r');
        try {
            $headers = $this->readHeaders($handle);
            if ($headers === null) {
                return;
            }

            $width = count($headers);
            while (($row = fgetcsv($handle, 0, $this->delimiter)) !== false) {
                if ($row === [null]) {
                    continue; // baris kosong
                }

                $row = array_slice(array_pad($row, $width, null), 0, $width);
                yield $this->mapper->map(array_combine($headers, $row));
            }
        } finally {
            fclose($handle);
        }
    }

    /** @param resource $handle @return list<string>|null */
    private function readHeaders($handle): ?array
    {
        $headers = fgetcsv($handle, 0, $this->delimiter);
        if ($headers === false || $headers === [null]) {
            return null;
        }

        return array_map(function (?string $header): string {
            $header = (string) $header;
            if (str_starts_with($header, self::UTF8_BOM)) {
                $header = substr($header, strlen(self::UTF8_BOM));
            }

            return trim($header);
        }, $headers);
    }
}
