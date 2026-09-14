<?php

declare(strict_types=1);

namespace Govia\WiseClient\Resources;

use Govia\WiseClient\DTO\ExchangeRateData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Queries\Rate\GetExchangeRateQuery;

final class RateResource
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return array<int, ExchangeRateData>
     */
    public function get(string $source, string $target): array
    {
        return (new GetExchangeRateQuery($this->http))->execute($source, $target);
    }
}
