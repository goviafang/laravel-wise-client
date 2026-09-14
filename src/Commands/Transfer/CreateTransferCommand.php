<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Transfer;

use Govia\WiseClient\DTO\CreateTransferData;
use Govia\WiseClient\DTO\TransferData;
use Govia\WiseClient\Http\HttpClientInterface;

final class CreateTransferCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(CreateTransferData $data): TransferData
    {
        $response = $this->http->post('/transfers', $data->toArray());

        return TransferData::fromArray($response->json);
    }
}
