<?php

declare(strict_types=1);

namespace Govia\WiseClient\Auth;

/**
 * A personal API token from Wise account settings. No expiry, no refresh.
 */
final class PersonalTokenAuthenticator implements AuthenticatorInterface
{
    public function __construct(private readonly string $token) {}

    public function token(): string
    {
        return $this->token;
    }
}
