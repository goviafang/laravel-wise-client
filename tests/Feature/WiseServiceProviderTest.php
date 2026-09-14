<?php

declare(strict_types=1);

use Govia\WiseClient\Auth\AccessToken;
use Govia\WiseClient\Auth\AuthenticatorInterface;
use Govia\WiseClient\Auth\ClientCredentialsAuthenticator;
use Govia\WiseClient\Auth\PersonalTokenAuthenticator;
use Govia\WiseClient\Auth\TokenRepositoryInterface;
use Govia\WiseClient\Auth\UserTokenAuthenticator;
use Govia\WiseClient\Webhooks\WebhookSignatureVerifier;

it('resolves a PersonalTokenAuthenticator for the personal_token driver', function () {
    $authenticator = app(AuthenticatorInterface::class);

    expect($authenticator)->toBeInstanceOf(PersonalTokenAuthenticator::class)
        ->and($authenticator->token())->toBe('test-token');
});

it('resolves a ClientCredentialsAuthenticator for the client_credentials driver', function () {
    config([
        'wise.auth.driver' => 'client_credentials',
        'wise.auth.client_credentials.client_id' => 'id',
        'wise.auth.client_credentials.client_secret' => 'secret',
    ]);

    expect(app(AuthenticatorInterface::class))->toBeInstanceOf(ClientCredentialsAuthenticator::class);
});

it('throws for an unsupported auth driver', function () {
    config(['wise.auth.driver' => 'not-a-real-driver']);

    app(AuthenticatorInterface::class);
})->throws(RuntimeException::class, 'Unsupported Wise auth driver [not-a-real-driver].');

it('throws for the user_token driver when no TokenRepositoryInterface is bound', function () {
    config(['wise.auth.driver' => 'user_token']);

    app(AuthenticatorInterface::class);
})->throws(RuntimeException::class, 'No TokenRepositoryInterface binding found.');

it('resolves a UserTokenAuthenticator once a TokenRepositoryInterface is bound', function () {
    config(['wise.auth.driver' => 'user_token']);

    app()->bind(TokenRepositoryInterface::class, fn () => new class implements TokenRepositoryInterface
    {
        public function get(): ?AccessToken
        {
            return null;
        }

        public function put(AccessToken $token): void {}
    });

    expect(app(AuthenticatorInterface::class))->toBeInstanceOf(UserTokenAuthenticator::class);
});

it('throws when the binding for the user_token driver does not implement TokenRepositoryInterface', function () {
    config(['wise.auth.driver' => 'user_token']);

    app()->bind(TokenRepositoryInterface::class, fn () => new stdClass);

    app(AuthenticatorInterface::class);
})->throws(RuntimeException::class, 'must implement TokenRepositoryInterface.');

it('throws when the webhook public key is not configured', function () {
    app(WebhookSignatureVerifier::class);
})->throws(RuntimeException::class, 'wise.webhook.public_key is not configured.');

it('resolves a WebhookSignatureVerifier once a public key is configured', function () {
    config(['wise.webhook.public_key' => 'not-a-real-key-but-non-empty']);

    expect(app(WebhookSignatureVerifier::class))->toBeInstanceOf(WebhookSignatureVerifier::class);
});
