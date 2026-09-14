<?php

declare(strict_types=1);

namespace Govia\WiseClient\Resources;

use Generator;
use Govia\WiseClient\Commands\Recipient\CreateRecipientCommand;
use Govia\WiseClient\Commands\Recipient\DeleteRecipientCommand;
use Govia\WiseClient\DTO\CreateRecipientData;
use Govia\WiseClient\DTO\RecipientData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Queries\Recipient\GetRecipientQuery;
use Govia\WiseClient\Queries\Recipient\ListRecipientsQuery;

final class RecipientResource
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function create(CreateRecipientData $data): RecipientData
    {
        return (new CreateRecipientCommand($this->http))->execute($data);
    }

    public function get(int $recipientId): RecipientData
    {
        return (new GetRecipientQuery($this->http))->execute($recipientId);
    }

    /**
     * @return Generator<int, RecipientData>
     */
    public function list(int $profileId): Generator
    {
        return (new ListRecipientsQuery($this->http))->execute($profileId);
    }

    public function delete(int $recipientId): void
    {
        (new DeleteRecipientCommand($this->http))->execute($recipientId);
    }
}
