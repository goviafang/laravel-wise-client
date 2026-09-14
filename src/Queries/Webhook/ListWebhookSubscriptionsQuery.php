<?php

declare(strict_types=1);

namespace Govia\WiseClient\Queries\Webhook;

use Govia\WiseClient\DTO\WebhookSubscriptionData;
use Govia\WiseClient\Http\HttpClientInterface;

final class ListWebhookSubscriptionsQuery
{
    public function __construct(private readonly HttpClientInterface $http) {}

    /**
     * @return array<int, WebhookSubscriptionData>
     */
    public function execute(int $profileId): array
    {
        $response = $this->http->get("/profiles/{$profileId}/subscriptions");

        return array_values(array_map(
            fn (mixed $item): WebhookSubscriptionData => WebhookSubscriptionData::fromArray(is_array($item) ? $item : []),
            $response->json,
        ));
    }
}
