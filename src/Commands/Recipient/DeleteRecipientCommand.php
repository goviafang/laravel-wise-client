<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Recipient;

use Govia\WiseClient\Http\HttpClientInterface;

final class DeleteRecipientCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $recipientId): void
    {
        $this->http->delete("/accounts/{$recipientId}");
    }
}
