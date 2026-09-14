<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Transfer;

use Govia\WiseClient\DTO\TransferData;
use Govia\WiseClient\Http\HttpClientInterface;

final class GetTransferQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $transferId): TransferData
    {
        $response = $this->http->get("/transfers/{$transferId}");

        return TransferData::fromArray($response->json);
    }
}
