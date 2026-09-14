<?php

declare(strict_types=1);

namespace Govia\WiseClient\Commands\Webhook;

use Govia\WiseClient\DTO\CreateWebhookSubscriptionData;
use Govia\WiseClient\DTO\WebhookSubscriptionData;
use Govia\WiseClient\Http\HttpClientInterface;

final class CreateWebhookSubscriptionCommand
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function execute(int $profileId, CreateWebhookSubscriptionData $data): WebhookSubscriptionData
    {
        $response = $this->http->post("/profiles/{$profileId}/subscriptions", $data->toArray());

        return WebhookSubscriptionData::fromArray($response->json);
    }
}
