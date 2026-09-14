<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Balance;

use Govia\WiseClient\DTO\BalanceData;
use Govia\WiseClient\Http\HttpClientInterface;

final class ListBalancesQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return array<int, BalanceData>
     */
    public function execute(int $profileId): array
    {
        $response = $this->http->get("/profiles/{$profileId}/balances", ['types' => 'STANDARD']);

        return array_values(array_map(
            fn (mixed $item): BalanceData => BalanceData::fromArray(is_array($item) ? $item : []),
            $response->json,
        ));
    }
}
