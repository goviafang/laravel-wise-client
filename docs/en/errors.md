# Error handling

Wise's own OpenAPI spec doesn't use a consistent error schema — most 4xx responses are defined inline, per endpoint, rather than through a shared component. This package doesn't try to mirror that inconsistency; every failed request gets normalized into one of these:

| HTTP status | Exception |
|---|---|
| 400 | `Govia\WiseClient\Exceptions\BadRequestException` |
| 401 | `Govia\WiseClient\Exceptions\UnauthorizedException` |
| 403 | `Govia\WiseClient\Exceptions\ForbiddenException` |
| 404 | `Govia\WiseClient\Exceptions\NotFoundException` |
| 422 | `Govia\WiseClient\Exceptions\ValidationException` |
| 429 | `Govia\WiseClient\Exceptions\RateLimitExceededException` |
| 5xx | `Govia\WiseClient\Exceptions\ServerException` |

All of them extend `WiseException`, so catching that covers everything:

```php
use Govia\WiseClient\Exceptions\WiseException;

try {
    Wise::transfers()->create($data);
} catch (WiseException $e) {
    $e->statusCode(); // int
    $e->body();        // array, whatever Wise sent back
}
```

`ValidationException` additionally exposes `errors()`, pulled from the response body's `errors` array when present. `RateLimitExceededException` exposes `retryAfterSeconds()`.

## SCA challenges are not HTTP errors

`ScaChallengeRequiredException` is deliberately **not** part of this hierarchy — it doesn't extend `WiseException`. A 403 that means "your transfer failed" and a 403 that means "complete a verification step and try again" are different situations for calling code to handle, so they're different exception types. See [authentication.md](authentication.md#strong-customer-authentication-sca).
