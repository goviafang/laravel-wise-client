<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Profile;

use Govia\WiseClient\DTO\ProfileData;
use Govia\WiseClient\Http\HttpClientInterface;

final class GetProfileQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $profileId): ProfileData
    {
        $response = $this->http->get("/profiles/{$profileId}");

        return ProfileData::fromArray($response->json);
    }
}
