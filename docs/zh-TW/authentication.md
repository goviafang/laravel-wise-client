# 認證方式

Wise 提供三種認證方式，這個套件透過 `Govia\WiseClient\Auth\AuthenticatorInterface` 三種都支援，用 `WISE_AUTH_DRIVER` 選一種。

## Personal Token

最簡單的方式，一組從你自己的 Wise 帳戶設定頁產生的靜態 token，沒有過期時間也不需要 refresh。

```env
WISE_AUTH_DRIVER=personal_token
WISE_PERSONAL_TOKEN=你的 token
```

## Client Credentials

適合 Wise Platform application 底下的伺服器對伺服器串接，走 OAuth2 的 `client_credentials` grant。Token 效期 12 小時，過期會自動重打換新的——這個 grant 沒有 refresh token，所以每次都是整顆重新認證。

```env
WISE_AUTH_DRIVER=client_credentials
WISE_CLIENT_ID=你的 client id
WISE_CLIENT_SECRET=你的 client secret
```

## User Token（OAuth2 authorization code）

代表某個 Wise 使用者授權你的 app 存取他的帳戶時使用。這是唯一需要套件幫忙持久化 token 的一種，因為 access token 會過期，需要 refresh token 才能續期。

```env
WISE_AUTH_DRIVER=user_token
WISE_CLIENT_ID=你的 client id
WISE_CLIENT_SECRET=你的 client secret
WISE_REDIRECT_URI=https://your-app.test/wise/callback
```

儲存機制由你實作 `Govia\WiseClient\Auth\TokenRepositoryInterface`，再到自己的 service provider 綁定：

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

套件本身刻意不附 migration 或 Eloquent model——token 要怎麼存、存哪（一個使用者一筆、要不要加密、放 Redis 還是 DB）是你的 app 該決定的事，不該由 client 套件替你決定。

### 授權流程

```php
use Govia\WiseClient\Auth\UserTokenAuthenticator;

$url = app(UserTokenAuthenticator::class)->authorizationUrl(scopes: ['transfers']);

return redirect($url);
```

```php
// callback route
app(UserTokenAuthenticator::class)->exchangeCode($request->query('code'));
```

換完 code 之後，token repository 裡就有一組有效的 access/refresh token，之後透過 `Wise::` 打的每個請求都會自動用它，過期了也會自動 refresh。

## 強驗證（SCA）

部分操作——特別是在有 SCA 規範的地區撥款——Wise 可能回傳一個驗證挑戰而不是直接完成操作。遇到這種情況套件會拋出 `Govia\WiseClient\Exceptions\ScaChallengeRequiredException`：

```php
use Govia\WiseClient\Exceptions\ScaChallengeRequiredException;

try {
    Wise::transfers()->fund($profileId, $transferId);
} catch (ScaChallengeRequiredException $e) {
    // $e->oneTimeToken() 跟 $e->availableMethods() 告訴你接下來要怎麼驗證
}
```

這個套件只負責偵測並拋出這個例外，不會幫你把 PIN、臉部辨識、裝置指紋這些驗證流程走完。這部分不在這一版的範圍內，詳見 [各資源用法](resources.md)。
