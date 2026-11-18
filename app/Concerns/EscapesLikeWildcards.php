<?php

namespace App\Concerns;

/** Escape karakter khusus LIKE agar input pengguna diperlakukan literal. */
trait EscapesLikeWildcards
{
    protected function escapeLike(string $value): string
    {
        return addcslashes($value, '\\%_');
    }
}
