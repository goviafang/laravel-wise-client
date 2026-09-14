<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

final class CreateWebhookSubscriptionData
{
    public function __construct(
        public readonly string $name,
        public readonly string $triggerOn,
        public readonly string $deliveryUrl,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'trigger_on' => $this->triggerOn,
            'delivery' => ['version' => '4.0.0', 'url' => $this->deliveryUrl],
        ];
    }
}
