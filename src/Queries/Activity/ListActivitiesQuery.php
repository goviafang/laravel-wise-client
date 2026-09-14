<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Activity;

use Generator;
use Govia\WiseClient\DTO\ActivityData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Http\Pagination\CursorPagination;
use Govia\WiseClient\Http\Paginator;

final class ListActivitiesQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return Generator<int, ActivityData>
     */
    public function execute(int $profileId): Generator
    {
        $paginator = new Paginator(
            $this->http,
            "/profiles/{$profileId}/activities",
            new CursorPagination(itemsKey: 'activities'),
        );

        foreach ($paginator as $item) {
            yield ActivityData::fromArray($item);
        }
    }
}
