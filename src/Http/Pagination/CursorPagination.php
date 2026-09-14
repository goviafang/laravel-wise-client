<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http\Pagination;

use Govia\WiseClient\Http\Response;

final class CursorPagination extends AbstractPagination
{
    public function __construct(
        ?string $itemsKey = null,
        private readonly string $requestParam = 'nextCursor',
        private readonly string $responseField = 'cursor',
        private readonly int $size = 100,
    ) {
        parent::__construct($itemsKey);
    }

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
}
