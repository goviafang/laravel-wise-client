<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Balance;

use Govia\WiseClient\DTO\BalanceData;
use Govia\WiseClient\Http\HttpClientInterface;

final class GetBalanceQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $profileId, int $balanceId): BalanceData
    {
        $response = $this->http->get("/profiles/{$profileId}/balances/{$balanceId}");

        return BalanceData::fromArray($response->json);
    }
}
