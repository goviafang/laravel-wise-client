<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class WebhookSubscriptionData
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $triggerOn,
        public readonly string $deliveryUrl,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $delivery = Caster::array($data['delivery'] ?? null);

        return new self(
            id: Caster::string($data['id'] ?? null),
            name: Caster::string($data['name'] ?? null),
            triggerOn: Caster::string($data['trigger_on'] ?? null),
            deliveryUrl: Caster::string($delivery['url'] ?? null),
        );
    }
}
