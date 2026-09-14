<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class BalanceData
{
    public function __construct(
        public readonly int $id,
        public readonly Money $money,
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
            money: new Money(
                amount: Caster::float($amount['value'] ?? null),
                currency: Caster::string($data['currency'] ?? null),
            ),
            type: Caster::string($data['type'] ?? null),
        );
    }
}
