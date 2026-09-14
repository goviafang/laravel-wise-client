<?php

declare(strict_types=1);

namespace Govia\WiseClient\Resources;

use Generator;
use Govia\WiseClient\Commands\Transfer\CancelTransferCommand;
use Govia\WiseClient\Commands\Transfer\CreateTransferCommand;
use Govia\WiseClient\Commands\Transfer\FundTransferCommand;
use Govia\WiseClient\DTO\CreateTransferData;
use Govia\WiseClient\DTO\TransferData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Queries\Transfer\GetTransferQuery;
use Govia\WiseClient\Queries\Transfer\ListTransfersQuery;

final class TransferResource
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function create(CreateTransferData $data): TransferData
    {
        return (new CreateTransferCommand($this->http))->execute($data);
    }

    public function get(int $transferId): TransferData
    {
        return (new GetTransferQuery($this->http))->execute($transferId);
    }

    /**
     * @return Generator<int, TransferData>
     */
    public function list(int $profileId): Generator
    {
        return (new ListTransfersQuery($this->http))->execute($profileId);
    }

    public function cancel(int $transferId): TransferData
    {
        return (new CancelTransferCommand($this->http))->execute($transferId);
    }

    public function fund(int $profileId, int $transferId): void
    {
        (new FundTransferCommand($this->http))->execute($profileId, $transferId);
    }
}
