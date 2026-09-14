<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Webhook;

use Govia\WiseClient\Http\HttpClientInterface;

final class DeleteWebhookSubscriptionCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $profileId, string $subscriptionId): void
    {
        $this->http->delete("/profiles/{$profileId}/subscriptions/{$subscriptionId}");
    }
}
