<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class BalanceData
{
    public function __construct(
        public readonly int $id,
        public readonly string $currency,
        public readonly float $amount,
        public readonly string $type,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $amount = Caster::array($data['amount'] ?? null);

        return new self(
            id: Caster::int($data['id'] ?? null),
            currency: Caster::string($data['currency'] ?? null),
            amount: Caster::float($amount['value'] ?? null),
            type: Caster::string($data['type'] ?? null),
        );
    }
}
