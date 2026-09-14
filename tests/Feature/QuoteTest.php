<?php

declare(strict_types=1);

use Govia\WiseClient\DTO\CreateQuoteData;
use Govia\WiseClient\WiseClient;
use Illuminate\Support\Facades\Http;

it('creates a quote', function () {
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
    ]);

    $quote = app(WiseClient::class)->quotes()->create(new CreateQuoteData(
        profileId: 123,
        sourceCurrency: 'GBP',
        targetCurrency: 'USD',
        sourceAmount: 100.0,
    ));

    expect($quote->id)->toBe('quote-uuid')
        ->and($quote->rate)->toBe(1.25)
        ->and($quote->source->amount)->toBe(100.0)
        ->and($quote->target->currency)->toBe('USD');

    Http::assertSent(fn ($request) => $request->url() === 'https://api.wise-sandbox.com/2026Q3/profiles/123/quotes'
        && $request['sourceCurrency'] === 'GBP'
        && $request->hasHeader('Authorization', 'Bearer test-token'));
});
