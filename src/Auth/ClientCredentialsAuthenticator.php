<?php

declare(strict_types=1);

namespace Govia\WiseClient\Auth;

/**
 * client_credentials grant: 12hr tokens, no refresh token, so a full re-request on expiry.
 * Cached only for this process (service container singleton), never persisted.
 */
final class ClientCredentialsAuthenticator implements AuthenticatorInterface
{
    private ?AccessToken $cached = null;

    public function __construct(private readonly OAuthTokenClient $oauth) {}

    public function token(): string
    {
        if ($this->cached === null || $this->cached->isExpired()) {
            $this->cached = $this->oauth->requestClientCredentialsToken();
        }

        return $this->cached->accessToken;
    }
}
