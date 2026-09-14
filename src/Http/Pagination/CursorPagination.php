<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http\Pagination;

use Govia\WiseClient\Http\PaginationStrategy;
use Govia\WiseClient\Http\Response;
use Govia\WiseClient\Support\Caster;

final class CursorPagination implements PaginationStrategy
{
    public function __construct(
        private readonly ?string $itemsKey = null,
        private readonly string $requestParam = 'nextCursor',
        private readonly string $responseField = 'cursor',
        private readonly int $size = 100,
    ) {}

    public function initialParams(): array
    {
        return ['size' => $this->size];
    }

    public function nextParams(Response $response, array $previousParams): ?array
    {
        $cursor = $response->json[$this->responseField] ?? null;

        if (empty($cursor)) {
            return null;
        }

        return [$this->requestParam => $cursor, 'size' => $this->size];
    }

    public function extractItems(Response $response): array
    {
        return $this->itemsKey === null
            ? Caster::listOfArrays($response->json)
            : Caster::listOfArrays($response->json[$this->itemsKey] ?? null);
    }
}
