<?php

declare(strict_types=1);

namespace Govia\WiseClient\Exceptions;

use Exception;
use Govia\WiseClient\Support\Caster;

class WiseException extends Exception
{
    /**
     * @param  array<array-key, mixed>  $body  raw error body from Wise; format isn't consistent across endpoints, kept as-is
     */
    public function __construct(
        string $message,
        protected readonly int $statusCode,
        protected readonly array $body = [],
    ) {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function body(): array
    {
        return $this->body;
    }

    /**
     * Maps an HTTP status to its exception subclass, falling back to the base class.
     *
     * @param  array<array-key, mixed>  $body
     */
    public static function fromResponse(int $statusCode, array $body): self
    {
        $message = Caster::string($body['message'] ?? $body['error'] ?? null, "Wise API returned HTTP {$statusCode}");

        $class = match ($statusCode) {
            400 => BadRequestException::class,
            401 => UnauthorizedException::class,
            403 => ForbiddenException::class,
            404 => NotFoundException::class,
            422 => ValidationException::class,
            429 => RateLimitExceededException::class,
            default => $statusCode >= 500 ? ServerException::class : self::class,
        };

        return new $class($message, $statusCode, $body);
    }
}
