<?php

declare(strict_types=1);

use Govia\WiseClient\DTO\CreateWebhookSubscriptionData;
use Govia\WiseClient\WiseClient;
use Illuminate\Support\Facades\Http;

it('subscribes to a webhook event', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/profiles/1/subscriptions' => Http::response([
            'id' => 'sub-uuid',
            'name' => 'Transfer updates',
            'trigger_on' => 'transfers#state-change',
            'delivery' => ['url' => 'https://your-app.test/wise/webhook'],
        ], 200),
    ]);

    $subscription = app(WiseClient::class)->webhooks()->subscribe(1, new CreateWebhookSubscriptionData(
        name: 'Transfer updates',
        triggerOn: 'transfers#state-change',
        deliveryUrl: 'https://your-app.test/wise/webhook',
    ));

    expect($subscription->id)->toBe('sub-uuid')
        ->and($subscription->deliveryUrl)->toBe('https://your-app.test/wise/webhook');

    Http::assertSent(fn ($request) => $request['trigger_on'] === 'transfers#state-change'
        && $request['delivery']['url'] === 'https://your-app.test/wise/webhook');
});

it('lists webhook subscriptions', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/profiles/1/subscriptions' => Http::response([
            ['id' => 'sub-1', 'name' => 'A', 'trigger_on' => 'transfers#state-change', 'delivery' => ['url' => 'https://a.test']],
            ['id' => 'sub-2', 'name' => 'B', 'trigger_on' => 'balances#update', 'delivery' => ['url' => 'https://b.test']],
        ], 200),
    ]);

    $subscriptions = app(WiseClient::class)->webhooks()->list(1);

    expect($subscriptions)->toHaveCount(2)
        ->and($subscriptions[0]->id)->toBe('sub-1')
        ->and($subscriptions[1]->triggerOn)->toBe('balances#update');
});

it('unsubscribes from a webhook event', function () {
    Http::fake([
        'https://api.wise-sandbox.com/2026Q3/profiles/1/subscriptions/sub-uuid' => Http::response([], 204),
    ]);

    app(WiseClient::class)->webhooks()->unsubscribe(1, 'sub-uuid');

    Http::assertSent(fn ($request) => $request->method() === 'DELETE'
        && str_contains($request->url(), '/subscriptions/sub-uuid'));
});
