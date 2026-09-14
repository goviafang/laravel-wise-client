<?php

declare(strict_types=1);

namespace Govia\WiseClient\Resources;

use DateTimeInterface;
use Govia\WiseClient\Commands\Balance\CreateBalanceCommand;
use Govia\WiseClient\DTO\BalanceData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Http\Response;
use Govia\WiseClient\Queries\Balance\GetBalanceQuery;
use Govia\WiseClient\Queries\Balance\GetBalanceStatementQuery;
use Govia\WiseClient\Queries\Balance\ListBalancesQuery;

final class BalanceResource
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function create(int $profileId, string $currency, string $idempotenceUuid): BalanceData
    {
        return (new CreateBalanceCommand($this->http))->execute($profileId, $currency, $idempotenceUuid);
    }

    /**
     * @return array<int, BalanceData>
     */
    public function list(int $profileId): array
    {
        return (new ListBalancesQuery($this->http))->execute($profileId);
    }

    public function get(int $profileId, int $balanceId): BalanceData
    {
        return (new GetBalanceQuery($this->http))->execute($profileId, $balanceId);
    }

    public function statement(int $profileId, int $balanceId, DateTimeInterface $from, DateTimeInterface $to): Response
    {
        return (new GetBalanceStatementQuery($this->http))->execute($profileId, $balanceId, $from, $to);
    }
}
