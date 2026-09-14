<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

final class CreateQuoteData
{
    public function __construct(
        public readonly int $profileId,
        public readonly string $sourceCurrency,
        public readonly string $targetCurrency,
        public readonly ?float $sourceAmount = null,
        public readonly ?float $targetAmount = null,
        public readonly ?int $targetAccount = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'sourceCurrency' => $this->sourceCurrency,
            'targetCurrency' => $this->targetCurrency,
            'sourceAmount' => $this->sourceAmount,
            'targetAmount' => $this->targetAmount,
            'targetAccount' => $this->targetAccount,
        ], fn ($value) => $value !== null);
    }
}
