<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

use Govia\WiseClient\Support\Caster;

final class ActivityData
{
    /**
     * @param  array<array-key, mixed>  $resource
     */
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $status,
        public readonly array $resource,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Caster::string($data['id'] ?? null),
            type: Caster::string($data['type'] ?? null),
            status: Caster::string($data['status'] ?? null),
            resource: Caster::array($data['resource'] ?? null),
        );
    }
}
