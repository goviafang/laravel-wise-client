<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class QuoteData
{
    public function __construct(
        public readonly string $id,
        public readonly Money $source,
        public readonly Money $target,
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
            source: new Money(
                amount: Caster::float($data['sourceAmount'] ?? null),
                currency: Caster::string($data['sourceCurrency'] ?? null),
            ),
            target: new Money(
                amount: Caster::float($data['targetAmount'] ?? null),
                currency: Caster::string($data['targetCurrency'] ?? null),
            ),
            rate: Caster::float($data['rate'] ?? null),
            status: Caster::string($data['status'] ?? null),
        );
    }
}
