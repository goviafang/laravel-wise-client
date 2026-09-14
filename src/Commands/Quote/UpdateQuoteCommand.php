<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Quote;

use Govia\WiseClient\DTO\QuoteData;
use Govia\WiseClient\Http\HttpClientInterface;

final class UpdateQuoteCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $profileId, string $quoteId, ?int $targetRecipientId = null): QuoteData
    {
        $response = $this->http->patch(
            "/profiles/{$profileId}/quotes/{$quoteId}",
            array_filter(['targetAccount' => $targetRecipientId])
        );

        return QuoteData::fromArray($response->json);
    }
}
