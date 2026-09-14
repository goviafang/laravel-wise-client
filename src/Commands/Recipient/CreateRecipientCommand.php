<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Recipient;

use Govia\WiseClient\DTO\CreateRecipientData;
use Govia\WiseClient\DTO\RecipientData;
use Govia\WiseClient\Http\HttpClientInterface;

final class CreateRecipientCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(CreateRecipientData $data): RecipientData
    {
        $response = $this->http->post('/accounts', $data->toArray());

        return RecipientData::fromArray($response->json);
    }
}
