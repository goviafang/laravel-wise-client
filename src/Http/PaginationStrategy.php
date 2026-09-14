<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http;

/**
 * Wise doesn't pick one pagination style (cursor, offset, seek, and page number all show up).
 * This interface separates "how to ask for the next page" from "how the caller consumes it",
 * so Paginator doesn't need to know the difference.
 */
interface PaginationStrategy
{
    /**
     * @return array<string, mixed>
     */
    public function initialParams(): array;

    /**
     * @param  array<string, mixed>  $previousParams
     * @return array<string, mixed>|null null means there's no next page
     */
    public function nextParams(Response $response, array $previousParams): ?array;

    /**
     * @return array<int, array<array-key, mixed>>
     */
    public function extractItems(Response $response): array;
}
