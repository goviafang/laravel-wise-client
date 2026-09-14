<?php

declare(strict_types=1);

namespace Govia\WiseClient\Resources;

use Govia\WiseClient\Commands\Webhook\CreateWebhookSubscriptionCommand;
use Govia\WiseClient\Commands\Webhook\DeleteWebhookSubscriptionCommand;
use Govia\WiseClient\DTO\CreateWebhookSubscriptionData;
use Govia\WiseClient\DTO\WebhookSubscriptionData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Queries\Webhook\ListWebhookSubscriptionsQuery;

final class WebhookResource
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function subscribe(int $profileId, CreateWebhookSubscriptionData $data): WebhookSubscriptionData
    {
        return (new CreateWebhookSubscriptionCommand($this->http))->execute($profileId, $data);
    }

    /**
     * @return array<int, WebhookSubscriptionData>
     */
    public function list(int $profileId): array
    {
        return (new ListWebhookSubscriptionsQuery($this->http))->execute($profileId);
    }

    public function unsubscribe(int $profileId, string $subscriptionId): void
    {
        (new DeleteWebhookSubscriptionCommand($this->http))->execute($profileId, $subscriptionId);
    }
}
