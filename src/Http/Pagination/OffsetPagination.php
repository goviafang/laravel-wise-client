<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http\Pagination;

use Govia\WiseClient\Http\Response;
use Govia\WiseClient\Support\Caster;

final class OffsetPagination extends AbstractPagination
{
    public function __construct(
        ?string $itemsKey = null,
        private readonly int $limit = 100,
    ) {
        parent::__construct($itemsKey);
    }

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
}
