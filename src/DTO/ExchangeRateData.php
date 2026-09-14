<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use DateTimeImmutable;
use Govia\WiseClient\Support\Caster;

final class ExchangeRateData
{
    public function __construct(
        public readonly string $source,
        public readonly string $target,
        public readonly float $rate,
        public readonly DateTimeImmutable $time,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            source: Caster::string($data['source'] ?? null),
            target: Caster::string($data['target'] ?? null),
            rate: Caster::float($data['rate'] ?? null),
            time: new DateTimeImmutable(Caster::string($data['time'] ?? null)),
        );
    }
}
