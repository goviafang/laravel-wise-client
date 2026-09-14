<?php

declare(strict_types=1);

use Govia\WiseClient\Exceptions\NotFoundException;
use Govia\WiseClient\Exceptions\RateLimitExceededException;
use Govia\WiseClient\WiseClient;
use Illuminate\Support\Facades\Http;

it('maps a 404 response to NotFoundException', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/transfers/999' => Http::response(['message' => 'not found'], 404),
    ]);

    app(WiseClient::class)->transfers()->get(999);
})->throws(NotFoundException::class);

it('maps a 429 response to RateLimitExceededException with retry-after', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/transfers/1' => Http::response(
            ['message' => 'rate limited'],
            429,
            ['Retry-After' => '5'],
        ),
    ]);

    try {
        app(WiseClient::class)->transfers()->get(1);
    } catch (RateLimitExceededException $e) {
        expect($e->retryAfterSeconds())->toBe(5);

        return;
    }

    $this->fail('Expected RateLimitExceededException was not thrown.');
});
