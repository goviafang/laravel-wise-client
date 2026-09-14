# 錯誤處理

Wise 自己的 OpenAPI spec 沒有統一的錯誤格式——大部分 4xx 回應都是每個 endpoint 各自 inline 定義，而不是共用同一個 schema。這個套件沒有照著這種不一致走，每個失敗的請求都會被正規化成下面其中一種：

| HTTP 狀態碼 | 例外類別 |
|---|---|
| 400 | `Govia\WiseClient\Exceptions\BadRequestException` |
| 401 | `Govia\WiseClient\Exceptions\UnauthorizedException` |
| 403 | `Govia\WiseClient\Exceptions\ForbiddenException` |
| 404 | `Govia\WiseClient\Exceptions\NotFoundException` |
| 422 | `Govia\WiseClient\Exceptions\ValidationException` |
| 429 | `Govia\WiseClient\Exceptions\RateLimitExceededException` |
| 5xx | `Govia\WiseClient\Exceptions\ServerException` |

這些全部繼承自 `WiseException`，所以只抓這一個就能涵蓋所有狀況：

```php
use Govia\WiseClient\Exceptions\WiseException;

try {
    Wise::transfers()->create($data);
} catch (WiseException $e) {
    $e->statusCode(); // int
    $e->body();        // array，Wise 回傳的原始內容
}
```

`ValidationException` 另外提供 `errors()`，從回應 body 的 `errors` 陣列取出來（如果有的話）。`RateLimitExceededException` 提供 `retryAfterSeconds()`。

## SCA 挑戰不算 HTTP 錯誤

`ScaChallengeRequiredException` 刻意**不**掛在這個階層底下——它沒有繼承 `WiseException`。同樣都是 403，一個代表「轉帳失敗」，另一個代表「先完成一次驗證再重試」，呼叫端要處理的邏輯完全不同，所以拆成兩種不同的例外類別。詳見 [認證方式](authentication.md#強驗證sca)。
