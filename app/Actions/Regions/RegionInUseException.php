<?php

namespace App\Actions\Regions;

use RuntimeException;

/** Wilayah masih dirujuk sekolah sehingga tidak dapat dihapus. */
class RegionInUseException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Wilayah masih dipakai oleh sekolah dan tidak dapat dihapus.');
    }
}
