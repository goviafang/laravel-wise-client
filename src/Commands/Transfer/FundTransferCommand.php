<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Transfer;

use Govia\WiseClient\Http\HttpClientInterface;

final class FundTransferCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * Funding can trigger SCA in some regions - the HTTP client throws
     * ScaChallengeRequiredException when that happens, and the caller
     * needs to complete verification before retrying.
     */
    public function execute(int $profileId, int $transferId): void
    {
        $this->http->post("/profiles/{$profileId}/transfers/{$transferId}/payments", [
            'type' => 'BALANCE',
        ]);
    }
}
