<?php

declare(strict_types=1);

use Govia\WiseClient\DTO\CreateTransferData;
use Govia\WiseClient\Exceptions\ScaChallengeRequiredException;
use Govia\WiseClient\WiseClient;
use Illuminate\Support\Facades\Http;

it('creates a transfer', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/transfers' => Http::response([
            'id' => 987,
            'status' => 'incoming_payment_waiting',
            'sourceCurrency' => 'GBP',
            'targetCurrency' => 'USD',
            'sourceValue' => 100.0,
            'targetValue' => 125.0,
            'customerTransactionId' => 'txn-uuid',
        ], 200),
    ]);

    $transfer = app(WiseClient::class)->transfers()->create(new CreateTransferData(
        quoteId: 'quote-uuid',
        targetRecipientId: 555,
        customerTransactionId: 'txn-uuid',
    ));

    expect($transfer->id)->toBe(987)
        ->and($transfer->source->amount)->toBe(100.0)
        ->and($transfer->source->currency)->toBe('GBP')
        ->and($transfer->target->amount)->toBe(125.0)
        ->and($transfer->target->currency)->toBe('USD');
});

it('throws a sca challenge exception when funding requires strong customer authentication', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/profiles/1/transfers/987/payments' => Http::response(
            ['error' => 'sca_required'],
            403,
            ['x-2fa-approval' => 'one-time-token-value'],
        ),
    ]);

    app(WiseClient::class)->transfers()->fund(1, 987);
})->throws(ScaChallengeRequiredException::class);
