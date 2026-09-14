<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http\Pagination;

use Govia\WiseClient\Http\PaginationStrategy;
use Govia\WiseClient\Http\Response;
use Govia\WiseClient\Support\Caster;

final class PageNumberPagination implements PaginationStrategy
{
    public function __construct(
        private readonly ?string $itemsKey = null,
        private readonly int $pageSize = 50,
    ) {}

    public function initialParams(): array
    {
        return ['pageSize' => $this->pageSize, 'pageNumber' => 1];
    }

    public function nextParams(Response $response, array $previousParams): ?array
    {
        $items = $this->extractItems($response);

        if (count($items) < $this->pageSize) {
            return null;
        }

        return ['pageSize' => $this->pageSize, 'pageNumber' => Caster::int($previousParams['pageNumber'] ?? null) + 1];
    }

    public function extractItems(Response $response): array
    {
        return $this->itemsKey === null
            ? Caster::listOfArrays($response->json)
            : Caster::listOfArrays($response->json[$this->itemsKey] ?? null);
    }
}
