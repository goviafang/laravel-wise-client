<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class RecipientData
{
    /**
     * @param  array<array-key, mixed>  $details
     */
    public function __construct(
        public readonly int $id,
        public readonly string $accountHolderName,
        public readonly string $currency,
        public readonly string $type,
        public readonly array $details,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Caster::int($data['id'] ?? null),
            accountHolderName: Caster::string($data['accountHolderName'] ?? null),
            currency: Caster::string($data['currency'] ?? null),
            type: Caster::string($data['type'] ?? null),
            details: Caster::array($data['details'] ?? null),
        );
    }
}
