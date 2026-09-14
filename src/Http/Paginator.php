<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http;

use Generator;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, array<array-key, mixed>>
 */
final class Paginator implements IteratorAggregate
{
    /**
     * @param  array<string, mixed>  $baseQuery
     */
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $uri,
        private readonly PaginationStrategy $strategy,
        private readonly array $baseQuery = [],
    ) {}

    /**
     * @return Generator<int, array<array-key, mixed>>
     */
    public function getIterator(): Generator
    {
        $params = $this->strategy->initialParams();

        while ($params !== null) {
            $response = $this->http->get($this->uri, $this->baseQuery + $params);

            // yield one by one, not yield from - avoids each page's own 0-based keys colliding
            foreach ($this->strategy->extractItems($response) as $item) {
                yield $item;
            }

            $params = $this->strategy->nextParams($response, $params);
        }
    }
}
