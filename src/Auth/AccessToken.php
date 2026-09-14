<?php

declare(strict_types=1);

namespace Govia\WiseClient\Auth;

use DateTimeImmutable;
use Govia\WiseClient\Support\Caster;

final class AccessToken
{
    public function __construct(
        public readonly string $accessToken,
        public readonly ?string $refreshToken,
        public readonly DateTimeImmutable $expiresAt,
    ) {}

    public function isExpired(): bool
    {
        // 30-second buffer so a token doesn't die mid-request
        return $this->expiresAt <= new DateTimeImmutable('+30 seconds');
    }

    /**
     * @param  array<array-key, mixed>  $response
     */
    public static function fromTokenResponse(array $response): self
    {
        $refreshToken = $response['refresh_token'] ?? null;

        return new self(
            accessToken: Caster::string($response['access_token'] ?? null),
            refreshToken: $refreshToken === null ? null : Caster::string($refreshToken),
            expiresAt: (new DateTimeImmutable)->modify('+'.Caster::int($response['expires_in'] ?? null).' seconds'),
        );
    }
}
