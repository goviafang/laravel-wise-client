<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

final class CreateRecipientData
{
    /**
     * @param  array<string, mixed>  $details  fields vary by country/currency, passed straight through to Wise
     */
    public function __construct(
        public readonly int $profileId,
        public readonly string $accountHolderName,
        public readonly string $currency,
        public readonly string $type,
        public readonly array $details,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'profile' => $this->profileId,
            'accountHolderName' => $this->accountHolderName,
            'currency' => $this->currency,
            'type' => $this->type,
            'details' => $this->details,
        ];
    }
}
