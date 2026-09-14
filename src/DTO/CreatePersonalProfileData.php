<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

final class CreatePersonalProfileData
{
    /**
     * @param  array<string, mixed>  $address  needs at least addressFirstLine, city, countryIso3Code
     * @param  array<string, mixed>  $contactDetails  needs at least email, phoneNumber
     * @param  array<string, mixed>  $extra  other optional fields (preferredName, occupation, etc.), passed straight through to Wise
     */
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $dateOfBirth,
        public readonly array $address,
        public readonly array $contactDetails,
        public readonly array $extra = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->extra + [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'dateOfBirth' => $this->dateOfBirth,
            'address' => $this->address,
            'contactDetails' => $this->contactDetails,
        ];
    }
}
