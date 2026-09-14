<?php

declare(strict_types=1);

namespace Govia\WiseClient\Auth;

use Govia\WiseClient\Exceptions\WiseException;
use Govia\WiseClient\Support\Caster;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Exchanges tokens with /oauth/token using Basic Auth. Kept separate from
 * HttpClientInterface because it's a different auth scheme than Bearer.
 */
final class OAuthTokenClient
{
    public function __construct(
        private readonly HttpFactory $http,
        private readonly string $baseUrl,
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {}

    public function requestClientCredentialsToken(): AccessToken
    {
        return $this->exchange(['grant_type' => 'client_credentials']);
    }

    public function exchangeAuthorizationCode(string $code, string $redirectUri): AccessToken
    {
        return $this->exchange([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ]);
    }

    public function refresh(string $refreshToken): AccessToken
    {
        return $this->exchange([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);
    }

    /**
     * @param  array<string, string>  $params
     */
    private function exchange(array $params): AccessToken
    {
        $response = $this->http
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post("{$this->baseUrl}/oauth/token", $params);

        $body = Caster::array($response->json());

        if ($response->failed()) {
            throw WiseException::fromResponse($response->status(), $body);
        }

        return AccessToken::fromTokenResponse($body);
    }
}
