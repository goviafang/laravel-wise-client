<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

final class CreateTransferData
{
    public function __construct(
        public readonly string $quoteId,
        public readonly int $targetRecipientId,
        public readonly string $customerTransactionId,
        public readonly ?string $reference = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'targetAccount' => $this->targetRecipientId,
            'quoteUuid' => $this->quoteId,
            // Wise has no shared idempotency header, so this field dedupes retries instead
            'customerTransactionId' => $this->customerTransactionId,
            'details' => $this->reference !== null ? ['reference' => $this->reference] : null,
        ], fn ($value) => $value !== null);
    }
}
