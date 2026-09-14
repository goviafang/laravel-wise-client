<?php

declare(strict_types=1);

use Govia\WiseClient\Auth\AccessToken;
use Govia\WiseClient\Auth\OAuthTokenClient;
use Govia\WiseClient\Auth\TokenRepositoryInterface;
use Govia\WiseClient\Auth\UserTokenAuthenticator;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Http;

function makeUserTokenAuthenticator(TokenRepositoryInterface $repository): UserTokenAuthenticator
{
    $oauth = new OAuthTokenClient(
        app(HttpFactory::class),
        'https://api.wise-sandbox.com',
        'client-id',
        'client-secret',
    );

    return new UserTokenAuthenticator(
        $oauth,
        $repository,
        'https://api.wise-sandbox.com/oauth/authorize',
        'client-id',
        'https://your-app.test/callback',
    );
}

function fakeTokenRepository(?AccessToken $initial = null): TokenRepositoryInterface
{
    return new class($initial) implements TokenRepositoryInterface
    {
        public function __construct(public ?AccessToken $token) {}

        public function get(): ?AccessToken
        {
            return $this->token;
        }

        public function put(AccessToken $token): void
        {
            $this->token = $token;
        }
    };
}

it('returns the stored token without refreshing when not expired', function () {
    Http::fake();

    $repository = fakeTokenRepository(new AccessToken('valid-token', 'refresh-token', new DateTimeImmutable('+1 hour')));

    expect(makeUserTokenAuthenticator($repository)->token())->toBe('valid-token');

    Http::assertNothingSent();
});

it('throws when no token has been stored yet', function () {
    $repository = fakeTokenRepository(null);

    makeUserTokenAuthenticator($repository)->token();
})->throws(RuntimeException::class, 'No Wise user token available.');

it('refreshes an expired token and persists the new one', function () {
    Http::fake([
        'https://api.wise-sandbox.com/oauth/token' => Http::response([
            'access_token' => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $repository = fakeTokenRepository(new AccessToken('expired-token', 'old-refresh-token', new DateTimeImmutable('-1 minute')));

    $token = makeUserTokenAuthenticator($repository)->token();

    expect($token)->toBe('new-access-token')
        ->and($repository->get()->accessToken)->toBe('new-access-token')
        ->and($repository->get()->refreshToken)->toBe('new-refresh-token');

    Http::assertSent(fn ($request) => $request['grant_type'] === 'refresh_token'
        && $request['refresh_token'] === 'old-refresh-token');
});

it('throws when the token is expired and there is no refresh token', function () {
    $repository = fakeTokenRepository(new AccessToken('expired-token', null, new DateTimeImmutable('-1 minute')));

    makeUserTokenAuthenticator($repository)->token();
})->throws(RuntimeException::class, 'Wise user token expired and no refresh token is available.');

it('builds an authorization url with the client id, redirect uri and scopes', function () {
    $repository = fakeTokenRepository(null);

    $url = makeUserTokenAuthenticator($repository)->authorizationUrl(['transfers', 'profiles']);

    expect($url)->toStartWith('https://api.wise-sandbox.com/oauth/authorize?')
        ->and($url)->toContain('client_id=client-id')
        ->and($url)->toContain('redirect_uri='.urlencode('https://your-app.test/callback'))
        ->and($url)->toContain('scope=transfers+profiles');
});

it('exchanges an authorization code and persists the resulting token', function () {
    Http::fake([
        'https://api.wise-sandbox.com/oauth/token' => Http::response([
            'access_token' => 'exchanged-access-token',
            'refresh_token' => 'exchanged-refresh-token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $repository = fakeTokenRepository(null);

    $token = makeUserTokenAuthenticator($repository)->exchangeCode('auth-code-123');

    expect($token->accessToken)->toBe('exchanged-access-token')
        ->and($repository->get()?->accessToken)->toBe('exchanged-access-token');

    Http::assertSent(fn ($request) => $request['grant_type'] === 'authorization_code'
        && $request['code'] === 'auth-code-123');
});
