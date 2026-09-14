<?php

declare(strict_types=1);

namespace Govia\WiseClient\Auth;

/**
 * authorization_code grant: a Wise user has authorized this app to act on their account.
 * Token storage is delegated to TokenRepositoryInterface; this class only auto-refreshes.
 */
final class UserTokenAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private readonly OAuthTokenClient $oauth,
        private readonly TokenRepositoryInterface $tokens,
        private readonly string $authorizeUrl,
        private readonly string $clientId,
        private readonly string $redirectUri,
    ) {}

    public function token(): string
    {
        $token = $this->tokens->get();

        if ($token === null) {
            throw new \RuntimeException(
                'No Wise user token available. Direct the user through authorizationUrl() first.'
            );
        }

        if ($token->isExpired()) {
            if ($token->refreshToken === null) {
                throw new \RuntimeException('Wise user token expired and no refresh token is available.');
            }

            $token = $this->oauth->refresh($token->refreshToken);
            $this->tokens->put($token);
        }

        return $token->accessToken;
    }

    /**
     * @param  array<int, string>  $scopes
     */
    public function authorizationUrl(array $scopes = []): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
        ]);

        return "{$this->authorizeUrl}?{$query}";
    }

    public function exchangeCode(string $code): AccessToken
    {
        $token = $this->oauth->exchangeAuthorizationCode($code, $this->redirectUri);
        $this->tokens->put($token);

        return $token;
    }
}
