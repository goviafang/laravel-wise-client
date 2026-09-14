<?php

declare(strict_types=1);

use Govia\WiseClient\DTO\CreateQuoteData;
use Govia\WiseClient\WiseClient;
use Illuminate\Support\Facades\Http;

it('throws when create() is called before quote() and recipient()', function () {
    app(WiseClient::class)->transferBuilder(profileId: 1)->create('txn-uuid');
})->throws(RuntimeException::class, 'Call quote() and recipient() before create().');

it('chains quote, recipient and fund into a single transfer', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/profiles/123/quotes' => Http::response([
            'id' => 'quote-uuid',
            'sourceCurrency' => 'GBP',
            'targetCurrency' => 'USD',
            'sourceAmount' => 100.0,
            'targetAmount' => 125.0,
            'rate' => 1.25,
            'status' => 'PENDING',
        ], 200),
        'https://api.wise-sandbox.com/2026Q3/transfers' => Http::response([
            'id' => 555,
            'status' => 'incoming_payment_waiting',
            'sourceCurrency' => 'GBP',
            'targetCurrency' => 'USD',
            'sourceValue' => 100.0,
            'targetValue' => 125.0,
            'customerTransactionId' => 'txn-uuid',
        ], 200),
        'https://api.wise-sandbox.com/2026Q3/profiles/123/transfers/555/payments' => Http::response([], 200),
    ]);

    $transfer = app(WiseClient::class)->transferBuilder(profileId: 123)
        ->quote(new CreateQuoteData(profileId: 123, sourceCurrency: 'GBP', targetCurrency: 'USD', sourceAmount: 100.0))
        ->recipient(999)
        ->fund()
        ->create('txn-uuid');

    expect($transfer->id)->toBe(555);

    Http::assertSent(fn ($request) => str_contains($request->url(), '/transfers/555/payments'));
});

it('does not fund the transfer unless fund() was called', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/profiles/123/quotes' => Http::response([
            'id' => 'quote-uuid',
            'sourceCurrency' => 'GBP',
            'targetCurrency' => 'USD',
            'sourceAmount' => 100.0,
            'targetAmount' => 125.0,
            'rate' => 1.25,
            'status' => 'PENDING',
        ], 200),
        'https://api.wise-sandbox.com/2026Q3/transfers' => Http::response([
            'id' => 555,
            'status' => 'incoming_payment_waiting',
            'sourceCurrency' => 'GBP',
            'targetCurrency' => 'USD',
            'sourceValue' => 100.0,
            'targetValue' => 125.0,
            'customerTransactionId' => 'txn-uuid',
        ], 200),
    ]);

    app(WiseClient::class)->transferBuilder(profileId: 123)
        ->quote(new CreateQuoteData(profileId: 123, sourceCurrency: 'GBP', targetCurrency: 'USD', sourceAmount: 100.0))
        ->recipient(999)
        ->create('txn-uuid');

    Http::assertNotSent(fn ($request) => str_contains($request->url(), '/payments'));
});
