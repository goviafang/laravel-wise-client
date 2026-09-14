<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class TransferData
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly string $sourceCurrency,
        public readonly string $targetCurrency,
        public readonly float $sourceAmount,
        public readonly float $targetAmount,
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
            sourceCurrency: Caster::string($data['sourceCurrency'] ?? null),
            targetCurrency: Caster::string($data['targetCurrency'] ?? null),
            sourceAmount: Caster::float($data['sourceValue'] ?? $data['sourceAmount'] ?? null),
            targetAmount: Caster::float($data['targetValue'] ?? $data['targetAmount'] ?? null),
            customerTransactionId: Caster::string($data['customerTransactionId'] ?? null),
        );
    }
}
