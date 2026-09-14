<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http\Pagination;

use Govia\WiseClient\Http\PaginationStrategy;
use Govia\WiseClient\Http\Response;
use Govia\WiseClient\Support\Caster;

final class SeekPagination implements PaginationStrategy
{
    public function __construct(
        private readonly ?string $itemsKey = null,
        private readonly string $seekField = 'seekPositionForNext',
        private readonly int $size = 20,
    ) {}

    public function initialParams(): array
    {
        return ['size' => $this->size];
    }

    public function nextParams(Response $response, array $previousParams): ?array
    {
        $next = $response->json[$this->seekField] ?? null;

        if (empty($next)) {
            return null;
        }

        return ['seekPosition' => $next, 'size' => $this->size];
    }

    public function extractItems(Response $response): array
    {
        return $this->itemsKey === null
            ? Caster::listOfArrays($response->json)
            : Caster::listOfArrays($response->json[$this->itemsKey] ?? null);
    }
}
