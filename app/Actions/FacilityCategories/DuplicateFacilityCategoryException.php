<?php

namespace App\Actions\FacilityCategories;

use RuntimeException;

/** Kombinasi nama + sub-kategori sudah ada (constraint uq_facility_category). */
class DuplicateFacilityCategoryException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Kategori dengan nama dan sub-kategori yang sama sudah ada.');
    }
}
