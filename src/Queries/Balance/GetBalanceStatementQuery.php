<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Balance;

use DateTimeInterface;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Http\Response;

final class GetBalanceStatementQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $profileId, int $balanceId, DateTimeInterface $from, DateTimeInterface $to): Response
    {
        return $this->http->get(
            "/profiles/{$profileId}/balance-statements/{$balanceId}/statement.json",
            [
                'intervalStart' => $from->format('Y-m-d\TH:i:s.000\Z'),
                'intervalEnd' => $to->format('Y-m-d\TH:i:s.000\Z'),
            ],
        );
    }
}
