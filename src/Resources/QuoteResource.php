<?php

declare(strict_types=1);

namespace Govia\WiseClient\Resources;

use Govia\WiseClient\Commands\Quote\CreateQuoteCommand;
use Govia\WiseClient\Commands\Quote\UpdateQuoteCommand;
use Govia\WiseClient\DTO\CreateQuoteData;
use Govia\WiseClient\DTO\QuoteData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Queries\Quote\GetQuoteQuery;

final class QuoteResource
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function create(CreateQuoteData $data): QuoteData
    {
        return (new CreateQuoteCommand($this->http))->execute($data);
    }

    public function get(int $profileId, string $quoteId): QuoteData
    {
        return (new GetQuoteQuery($this->http))->execute($profileId, $quoteId);
    }

    public function update(int $profileId, string $quoteId, ?int $targetRecipientId = null): QuoteData
    {
        return (new UpdateQuoteCommand($this->http))->execute($profileId, $quoteId, $targetRecipientId);
    }
}
