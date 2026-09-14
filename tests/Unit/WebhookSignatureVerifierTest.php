<?php

declare(strict_types=1);

use Govia\WiseClient\Webhooks\WebhookSignatureVerifier;

function generateTestKeyPair(): array
{
    $resource = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($resource, $privateKey);
    $publicKey = openssl_pkey_get_details($resource)['key'];

    return [$privateKey, $publicKey];
}

it('accepts a correctly signed payload', function () {
    [$privateKey, $publicKey] = generateTestKeyPair();
    $body = json_encode(['event_type' => 'transfers#state-change']);

    openssl_sign($body, $signature, $privateKey, OPENSSL_ALGO_SHA256);

    $verifier = new WebhookSignatureVerifier($publicKey);

    expect($verifier->verify($body, base64_encode($signature)))->toBeTrue();
});

it('rejects a tampered payload', function () {
    [$privateKey, $publicKey] = generateTestKeyPair();
    $body = json_encode(['event_type' => 'transfers#state-change']);

    openssl_sign($body, $signature, $privateKey, OPENSSL_ALGO_SHA256);

    $verifier = new WebhookSignatureVerifier($publicKey);

    expect($verifier->verify('{"tampered":true}', base64_encode($signature)))->toBeFalse();
});
