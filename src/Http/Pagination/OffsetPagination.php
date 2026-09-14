<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http\Pagination;

use Govia\WiseClient\Http\PaginationStrategy;
use Govia\WiseClient\Http\Response;
use Govia\WiseClient\Support\Caster;

final class OffsetPagination implements PaginationStrategy
{
    public function __construct(
        private readonly ?string $itemsKey = null,
        private readonly int $limit = 100,
    ) {}

    public function initialParams(): array
    {
        return ['limit' => $this->limit, 'offset' => 0];
    }

    public function nextParams(Response $response, array $previousParams): ?array
    {
        $items = $this->extractItems($response);

        if (count($items) < $this->limit) {
            return null;
        }

        return ['limit' => $this->limit, 'offset' => Caster::int($previousParams['offset'] ?? null) + $this->limit];
    }

    public function extractItems(Response $response): array
    {
        return $this->itemsKey === null
            ? Caster::listOfArrays($response->json)
            : Caster::listOfArrays($response->json[$this->itemsKey] ?? null);
    }
}
