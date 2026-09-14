# Authentication

Wise exposes three ways to authenticate, and this package supports all three through `Govia\WiseClient\Auth\AuthenticatorInterface`. Pick one with `WISE_AUTH_DRIVER`.

## Personal token

The simplest option — a static token you generate from your own Wise account settings. No expiry, no refresh.

```env
WISE_AUTH_DRIVER=personal_token
WISE_PERSONAL_TOKEN=your-token
```

## Client credentials

For server-to-server access under a Wise Platform application, using the `client_credentials` OAuth2 grant. Tokens last 12 hours and are re-requested automatically when they expire — there's no refresh token for this grant, so it's a full re-authentication each time.

```env
WISE_AUTH_DRIVER=client_credentials
WISE_CLIENT_ID=your-client-id
WISE_CLIENT_SECRET=your-client-secret
```

## User token (OAuth2 authorization code)

For acting on behalf of a Wise user who has authorized your application. This is the only driver where the package needs somewhere to persist tokens between requests, because access tokens expire and need a refresh token to renew.

```env
WISE_AUTH_DRIVER=user_token
WISE_CLIENT_ID=your-client-id
WISE_CLIENT_SECRET=your-client-secret
WISE_REDIRECT_URI=https://your-app.test/wise/callback
```

You provide the storage by implementing `Govia\WiseClient\Auth\TokenRepositoryInterface` and binding it in your own service provider:

```php
use Govia\WiseClient\Auth\AccessToken;
use Govia\WiseClient\Auth\TokenRepositoryInterface;

class DatabaseTokenRepository implements TokenRepositoryInterface
{
    public function get(): ?AccessToken
    {
        $row = WiseToken::first();

        return $row ? new AccessToken($row->access_token, $row->refresh_token, $row->expires_at) : null;
    }

    public function put(AccessToken $token): void
    {
        WiseToken::updateOrCreate([], [
            'access_token' => $token->accessToken,
            'refresh_token' => $token->refreshToken,
            'expires_at' => $token->expiresAt,
        ]);
    }
}
```

```php
// AppServiceProvider::register()
$this->app->bind(TokenRepositoryInterface::class, DatabaseTokenRepository::class);
```

The package deliberately doesn't ship a migration or Eloquent model for this — how and where you store tokens (one row per user, encrypted at rest, in Redis, wherever) is a decision for your app, not this package.

### Authorization flow

```php
use Govia\WiseClient\Auth\UserTokenAuthenticator;

$url = app(UserTokenAuthenticator::class)->authorizationUrl(scopes: ['transfers']);

return redirect($url);
```

```php
// callback route
app(UserTokenAuthenticator::class)->exchangeCode($request->query('code'));
```

After the code exchange, the token repository holds a valid access/refresh token pair and every subsequent request through `Wise::` uses it automatically, refreshing as needed.

## Strong Customer Authentication (SCA)

Some operations — transfer funding in particular, in regions where SCA applies — can respond with a challenge instead of completing. The package throws `Govia\WiseClient\Exceptions\ScaChallengeRequiredException` when that happens:

```php
use Govia\WiseClient\Exceptions\ScaChallengeRequiredException;

try {
    Wise::transfers()->fund($profileId, $transferId);
} catch (ScaChallengeRequiredException $e) {
    // $e->oneTimeToken() and $e->availableMethods() tell you what to do next
}
```

This package only detects the challenge and surfaces it — it doesn't drive the PIN, face-scan, or device-fingerprint verification flows themselves. Those are out of scope for this release; see [resources.md](resources.md).
