<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http\Pagination;

use Govia\WiseClient\Http\PaginationStrategy;
use Govia\WiseClient\Http\Response;
use Govia\WiseClient\Support\Caster;

abstract class AbstractPagination implements PaginationStrategy
{
    public function __construct(protected readonly ?string $itemsKey = null) {}

    public function extractItems(Response $response): array
    {
        return $this->itemsKey === null
            ? Caster::listOfArrays($response->json)
            : Caster::listOfArrays($response->json[$this->itemsKey] ?? null);
    }

    abstract public function initialParams(): array;

    abstract public function nextParams(Response $response, array $previousParams): ?array;
}
