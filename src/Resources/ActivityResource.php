<?php

declare(strict_types=1);

namespace Govia\WiseClient\Resources;

use Generator;
use Govia\WiseClient\DTO\ActivityData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Queries\Activity\ListActivitiesQuery;

final class ActivityResource
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return Generator<int, ActivityData>
     */
    public function list(int $profileId): Generator
    {
        return (new ListActivitiesQuery($this->http))->execute($profileId);
    }
}
