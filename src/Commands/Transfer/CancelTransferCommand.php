<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Transfer;

use Govia\WiseClient\DTO\TransferData;
use Govia\WiseClient\Http\HttpClientInterface;

final class CancelTransferCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $transferId): TransferData
    {
        $response = $this->http->put("/transfers/{$transferId}/cancel");

        return TransferData::fromArray($response->json);
    }
}
