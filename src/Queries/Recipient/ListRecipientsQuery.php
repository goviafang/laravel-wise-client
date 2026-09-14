<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Recipient;

use Generator;
use Govia\WiseClient\DTO\RecipientData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Http\Pagination\SeekPagination;
use Govia\WiseClient\Http\Paginator;

final class ListRecipientsQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return Generator<int, RecipientData>
     */
    public function execute(int $profileId): Generator
    {
        $paginator = new Paginator(
            $this->http,
            '/accounts',
            new SeekPagination(itemsKey: 'content'),
            ['profile' => $profileId],
        );

        foreach ($paginator as $item) {
            yield RecipientData::fromArray($item);
        }
    }
}
