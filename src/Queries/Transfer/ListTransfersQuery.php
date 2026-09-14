<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Transfer;

use Generator;
use Govia\WiseClient\DTO\TransferData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Http\Pagination\OffsetPagination;
use Govia\WiseClient\Http\Paginator;

final class ListTransfersQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return Generator<int, TransferData>
     */
    public function execute(int $profileId): Generator
    {
        $paginator = new Paginator(
            $this->http,
            '/transfers',
            new OffsetPagination,
            ['profile' => $profileId],
        );

        foreach ($paginator as $item) {
            yield TransferData::fromArray($item);
        }
    }
}
