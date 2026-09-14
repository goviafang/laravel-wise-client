<?php

declare(strict_types=1);

namespace Govia\WiseClient\Webhooks;

final class WebhookSignatureVerifier
{
    public function __construct(private readonly string $publicKeyPem) {}

    public function verify(string $rawBody, string $signatureBase64): bool
    {
        $publicKey = openssl_pkey_get_public($this->publicKeyPem);

        if ($publicKey === false) {
            return false;
        }

        $signature = base64_decode($signatureBase64, strict: true);

        if ($signature === false) {
            return false;
        }

        return openssl_verify($rawBody, $signature, $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }
}
