<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Quote;

use Govia\WiseClient\DTO\CreateQuoteData;
use Govia\WiseClient\DTO\QuoteData;
use Govia\WiseClient\Http\HttpClientInterface;

final class CreateQuoteCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(CreateQuoteData $data): QuoteData
    {
        $response = $this->http->post("/profiles/{$data->profileId}/quotes", $data->toArray());

        return QuoteData::fromArray($response->json);
    }
}
