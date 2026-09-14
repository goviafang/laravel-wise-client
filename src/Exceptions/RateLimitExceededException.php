<?php

declare(strict_types=1);

namespace Govia\WiseClient\Exceptions;

class RateLimitExceededException extends WiseException
{
    /**
     * @param  array<array-key, mixed>  $body
     */
    public function __construct(
        string $message,
        int $statusCode,
        array $body = [],
        private readonly ?int $retryAfterSeconds = null,
    ) {
        parent::__construct($message, $statusCode, $body);
    }

    public function retryAfterSeconds(): ?int
    {
        return $this->retryAfterSeconds;
    }
}
