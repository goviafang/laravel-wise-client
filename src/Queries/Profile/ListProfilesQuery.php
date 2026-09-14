<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Profile;

use Govia\WiseClient\DTO\ProfileData;
use Govia\WiseClient\Http\HttpClientInterface;

final class ListProfilesQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return array<int, ProfileData>
     */
    public function execute(): array
    {
        $response = $this->http->get('/profiles');

        return array_values(array_map(
            fn (mixed $item): ProfileData => ProfileData::fromArray(is_array($item) ? $item : []),
            $response->json,
        ));
    }
}
