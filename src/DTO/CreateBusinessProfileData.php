<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

final class CreateBusinessProfileData
{
    /**
     * @param  array<string, mixed>  $extra  other fields (companyType, registrationNumber, etc.), passed straight through to Wise
     */
    public function __construct(
        public readonly string $businessName,
        public readonly array $extra = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->extra + [
            'businessName' => $this->businessName,
        ];
    }
}
