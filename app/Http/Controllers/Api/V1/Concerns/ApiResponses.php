<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/** Format respons seragam: { data, meta? } dan { message, errors? }. */
trait ApiResponses
{
    protected function ok(mixed $data, ?array $meta = null, int $status = 200): JsonResponse
    {
        $body = ['data' => $data];
        if ($meta !== null) {
            $body['meta'] = $meta;
        }

        return response()->json($body, $status);
    }

    protected function fail(int $status, string $message, ?array $errors = null): JsonResponse
    {
        $body = ['message' => $message];
        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $status);
    }

    protected function pageMeta(LengthAwarePaginator $page): array
    {
        return [
            'page' => $page->currentPage(),
            'per_page' => $page->perPage(),
            'total' => $page->total(),
        ];
    }

    /** Escape karakter khusus LIKE agar input pengguna diperlakukan literal. */
    protected function escapeLike(string $value): string
    {
        return addcslashes($value, '\\%_');
    }
}
