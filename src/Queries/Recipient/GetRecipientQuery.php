<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Recipient;

use Govia\WiseClient\DTO\RecipientData;
use Govia\WiseClient\Http\HttpClientInterface;

final class GetRecipientQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $recipientId): RecipientData
    {
        $response = $this->http->get("/accounts/{$recipientId}");

        return RecipientData::fromArray($response->json);
    }
}
