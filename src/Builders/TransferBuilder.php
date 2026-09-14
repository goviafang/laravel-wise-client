<?php

declare(strict_types=1);

namespace Govia\WiseClient\Builders;

use Govia\WiseClient\DTO\CreateQuoteData;
use Govia\WiseClient\DTO\CreateTransferData;
use Govia\WiseClient\DTO\QuoteData;
use Govia\WiseClient\DTO\TransferData;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Resources\QuoteResource;
use Govia\WiseClient\Resources\TransferResource;
use RuntimeException;

/**
 * Chains quote -> recipient -> transfer -> fund into one call.
 * Just calls the same Resources\ methods underneath, so you can still do each step by hand.
 */
final class TransferBuilder
{
    private ?QuoteData $quote = null;

    private ?int $recipientId = null;

    private bool $shouldFund = false;

    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly int $profileId,
    ) {}

    public function quote(CreateQuoteData $data): self
    {
        $this->quote = (new QuoteResource($this->http))->create($data);

        return $this;
    }

    public function recipient(int $recipientId): self
    {
        $this->recipientId = $recipientId;

        return $this;
    }

    public function fund(): self
    {
        $this->shouldFund = true;

        return $this;
    }

    public function create(string $customerTransactionId, ?string $reference = null): TransferData
    {
        if ($this->quote === null || $this->recipientId === null) {
            throw new RuntimeException('Call quote() and recipient() before create().');
        }

        $transfers = new TransferResource($this->http);

        $transfer = $transfers->create(new CreateTransferData(
            quoteId: $this->quote->id,
            targetRecipientId: $this->recipientId,
            customerTransactionId: $customerTransactionId,
            reference: $reference,
        ));

        if ($this->shouldFund) {
            $transfers->fund($this->profileId, $transfer->id);
        }

        return $transfer;
    }
}
