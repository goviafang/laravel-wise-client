<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Profile;

use Govia\WiseClient\DTO\CreatePersonalProfileData;
use Govia\WiseClient\DTO\ProfileData;
use Govia\WiseClient\Http\HttpClientInterface;

final class CreatePersonalProfileCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(CreatePersonalProfileData $data): ProfileData
    {
        $response = $this->http->post('/profiles/personal-profile', $data->toArray());

        return ProfileData::fromArray($response->json);
    }
}
