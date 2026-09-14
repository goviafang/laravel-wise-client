<?php

declare(strict_types=1);

namespace Govia\WiseClient\Webhooks\Events;

final class WiseWebhookReceived
{
    /**
     * @param  array<array-key, mixed>  $payload
     */
    public function __construct(
        public readonly string $eventType,
        public readonly array $payload,
    ) {}
}
