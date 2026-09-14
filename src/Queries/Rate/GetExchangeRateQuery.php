<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Rate;

use Govia\WiseClient\DTO\ExchangeRateData;
use Govia\WiseClient\Http\HttpClientInterface;

final class GetExchangeRateQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return array<int, ExchangeRateData>
     */
    public function execute(string $source, string $target): array
    {
        $response = $this->http->get('/rates', ['source' => $source, 'target' => $target]);

        return array_values(array_map(
            fn (mixed $item): ExchangeRateData => ExchangeRateData::fromArray(is_array($item) ? $item : []),
            $response->json,
        ));
    }
}
