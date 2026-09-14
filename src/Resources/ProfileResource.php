<?php

declare(strict_types=1);

namespace Govia\WiseClient\Resources;

use Govia\WiseClient\Commands\Profile\CreateBusinessProfileCommand;
use Govia\WiseClient\Commands\Profile\CreatePersonalProfileCommand;
use Govia\WiseClient\DTO\CreateBusinessProfileData;
use Govia\WiseClient\DTO\CreatePersonalProfileData;
use Govia\WiseClient\DTO\ProfileData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Queries\Profile\GetProfileQuery;
use Govia\WiseClient\Queries\Profile\ListProfilesQuery;

final class ProfileResource
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function createPersonal(CreatePersonalProfileData $data): ProfileData
    {
        return (new CreatePersonalProfileCommand($this->http))->execute($data);
    }

    public function createBusiness(CreateBusinessProfileData $data): ProfileData
    {
        return (new CreateBusinessProfileCommand($this->http))->execute($data);
    }

    public function get(int $profileId): ProfileData
    {
        return (new GetProfileQuery($this->http))->execute($profileId);
    }

    /**
     * @return array<int, ProfileData>
     */
    public function list(): array
    {
        return (new ListProfilesQuery($this->http))->execute();
    }
}
