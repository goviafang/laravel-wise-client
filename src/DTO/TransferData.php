<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class TransferData
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly Money $source,
        public readonly Money $target,
        public readonly string $customerTransactionId,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Caster::int($data['id'] ?? null),
            status: Caster::string($data['status'] ?? null),
            source: new Money(
                amount: Caster::float($data['sourceValue'] ?? $data['sourceAmount'] ?? null),
                currency: Caster::string($data['sourceCurrency'] ?? null),
            ),
            target: new Money(
                amount: Caster::float($data['targetValue'] ?? $data['targetAmount'] ?? null),
                currency: Caster::string($data['targetCurrency'] ?? null),
            ),
            customerTransactionId: Caster::string($data['customerTransactionId'] ?? null),
        );
    }
}
