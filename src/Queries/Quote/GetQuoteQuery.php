<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Quote;

use Govia\WiseClient\DTO\QuoteData;
use Govia\WiseClient\Http\HttpClientInterface;

final class GetQuoteQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $profileId, string $quoteId): QuoteData
    {
        $response = $this->http->get("/profiles/{$profileId}/quotes/{$quoteId}");

        return QuoteData::fromArray($response->json);
    }
}
