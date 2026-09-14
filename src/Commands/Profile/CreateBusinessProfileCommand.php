<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Profile;

use Govia\WiseClient\DTO\CreateBusinessProfileData;
use Govia\WiseClient\DTO\ProfileData;
use Govia\WiseClient\Http\HttpClientInterface;

final class CreateBusinessProfileCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(CreateBusinessProfileData $data): ProfileData
    {
        $response = $this->http->post('/profiles/business-profile', $data->toArray());

        return ProfileData::fromArray($response->json);
    }
}
