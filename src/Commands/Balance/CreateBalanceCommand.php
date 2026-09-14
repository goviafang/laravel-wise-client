<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Balance;

use Govia\WiseClient\DTO\BalanceData;
use Govia\WiseClient\Http\HttpClientInterface;

final class CreateBalanceCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $profileId, string $currency, string $idempotenceUuid): BalanceData
    {
        $response = $this->http->post(
            "/profiles/{$profileId}/balances",
            ['currency' => $currency, 'type' => 'STANDARD'],
            ['X-idempotence-uuid' => $idempotenceUuid],
        );

        return BalanceData::fromArray($response->json);
    }
}
