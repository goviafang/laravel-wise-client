<?php

declare(strict_types=1);

namespace Govia\WiseClient\Webhooks;

use Govia\WiseClient\Support\Caster;
use Govia\WiseClient\Webhooks\Events\WiseWebhookReceived;
use Illuminate\Contracts\Events\Dispatcher;

final class WebhookEventDispatcher
{
    public function __construct(private readonly Dispatcher $events) {}

    /**
     * @param  array<array-key, mixed>  $payload
     */
    public function dispatch(array $payload): void
    {
        $this->events->dispatch(new WiseWebhookReceived(
            eventType: Caster::string($payload['event_type'] ?? null),
            payload: $payload,
        ));
    }
}
