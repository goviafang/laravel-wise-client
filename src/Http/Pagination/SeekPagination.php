<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http\Pagination;

use Govia\WiseClient\Http\Response;

final class SeekPagination extends AbstractPagination
{
    public function __construct(
        ?string $itemsKey = null,
        private readonly string $seekField = 'seekPositionForNext',
        private readonly int $size = 20,
    ) {
        parent::__construct($itemsKey);
    }

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
}
