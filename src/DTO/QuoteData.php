<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class QuoteData
{
    public function __construct(
        public readonly string $id,
        public readonly string $sourceCurrency,
        public readonly string $targetCurrency,
        public readonly float $sourceAmount,
        public readonly float $targetAmount,
        public readonly float $rate,
        public readonly string $status,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Caster::string($data['id'] ?? null),
            sourceCurrency: Caster::string($data['sourceCurrency'] ?? null),
            targetCurrency: Caster::string($data['targetCurrency'] ?? null),
            sourceAmount: Caster::float($data['sourceAmount'] ?? null),
            targetAmount: Caster::float($data['targetAmount'] ?? null),
            rate: Caster::float($data['rate'] ?? null),
            status: Caster::string($data['status'] ?? null),
        );
    }
}
