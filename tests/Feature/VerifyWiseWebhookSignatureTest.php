<?php

declare(strict_types=1);

use Govia\WiseClient\Webhooks\Events\WiseWebhookReceived;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

function generateWebhookKeyPair(): array
{
    $resource = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($resource, $privateKey);
    $publicKey = openssl_pkey_get_details($resource)['key'];

    return [$privateKey, $publicKey];
}

function signWebhookBody(string $body, string $privateKey): string
{
    openssl_sign($body, $signature, $privateKey, OPENSSL_ALGO_SHA256);

    return base64_encode($signature);
}

beforeEach(function () {
    Route::post('/wise-webhook-test', fn () => response()->noContent())
        ->middleware('wise.verify-signature');
});

it('accepts a correctly signed webhook and dispatches the event', function () {
    Event::fake();

    [$privateKey, $publicKey] = generateWebhookKeyPair();
    config(['wise.webhook.public_key' => $publicKey]);

    $body = json_encode(['event_type' => 'transfers#state-change']);
    $headers = $this->transformHeadersToServerVars([
        'Content-Type' => 'application/json',
        'X-Signature-SHA256' => signWebhookBody($body, $privateKey),
    ]);

    $response = $this->call('POST', '/wise-webhook-test', [], [], [], $headers, $body);

    $response->assertNoContent();

    Event::assertDispatched(WiseWebhookReceived::class, fn ($event) => $event->eventType === 'transfers#state-change');
});

it('rejects a webhook with an invalid signature', function () {
    [, $publicKey] = generateWebhookKeyPair();
    config(['wise.webhook.public_key' => $publicKey]);

    $body = json_encode(['event_type' => 'transfers#state-change']);
    $headers = $this->transformHeadersToServerVars([
        'Content-Type' => 'application/json',
        'X-Signature-SHA256' => base64_encode('not-a-real-signature'),
    ]);

    $response = $this->call('POST', '/wise-webhook-test', [], [], [], $headers, $body);

    $response->assertStatus(401);
});

it('rejects a webhook with no signature header', function () {
    [, $publicKey] = generateWebhookKeyPair();
    config(['wise.webhook.public_key' => $publicKey]);

    $body = json_encode(['event_type' => 'transfers#state-change']);
    $headers = $this->transformHeadersToServerVars(['Content-Type' => 'application/json']);

    $response = $this->call('POST', '/wise-webhook-test', [], [], [], $headers, $body);

    $response->assertStatus(401);
});
