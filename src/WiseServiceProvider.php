<?php

declare(strict_types=1);

namespace Govia\WiseClient;

use Govia\WiseClient\Auth\AuthenticatorInterface;
use Govia\WiseClient\Auth\ClientCredentialsAuthenticator;
use Govia\WiseClient\Auth\OAuthTokenClient;
use Govia\WiseClient\Auth\PersonalTokenAuthenticator;
use Govia\WiseClient\Auth\TokenRepositoryInterface;
use Govia\WiseClient\Auth\UserTokenAuthenticator;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Http\LaravelHttpClient;
use Govia\WiseClient\Support\Caster;
use Govia\WiseClient\Webhooks\Middleware\VerifyWiseWebhookSignature;
use Govia\WiseClient\Webhooks\WebhookEventDispatcher;
use Govia\WiseClient\Webhooks\WebhookSignatureVerifier;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

final class WiseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wise.php', 'wise');

        $this->app->singleton(AuthenticatorInterface::class, function (Application $app) {
            $config = $this->wiseConfig($app);

            return match ($config['auth']['driver']) {
                'personal_token' => new PersonalTokenAuthenticator(
                    Caster::string($config['auth']['personal_token']['token'])
                ),
                'client_credentials' => new ClientCredentialsAuthenticator(
                    $this->makeOAuthTokenClient($app, $config, $config['auth']['client_credentials'])
                ),
                'user_token' => $this->makeUserTokenAuthenticator($app, $config),
                default => throw new RuntimeException("Unsupported Wise auth driver [{$config['auth']['driver']}]."),
            };
        });

        $this->app->singleton(HttpClientInterface::class, function (Application $app) {
            $config = $this->wiseConfig($app);
            $baseUrl = rtrim($config['servers'][$config['environment']], '/').'/'.$config['api_version'];

            return new LaravelHttpClient(
                $app->make(HttpFactory::class),
                $app->make(AuthenticatorInterface::class),
                $baseUrl,
                $config['http']['timeout'],
                $config['http']['retry']['enabled'],
                $config['http']['retry']['max_attempts'],
            );
        });

        $this->app->singleton(
            WiseClient::class,
            fn (Application $app) => new WiseClient($app->make(HttpClientInterface::class))
        );

        $this->app->singleton(WebhookSignatureVerifier::class, function (Application $app) {
            $publicKey = $this->wiseConfig($app)['webhook']['public_key'];

            if (empty($publicKey)) {
                throw new RuntimeException('wise.webhook.public_key is not configured.');
            }

            return new WebhookSignatureVerifier($publicKey);
        });

        $this->app->singleton(WebhookEventDispatcher::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/wise.php' => config_path('wise.php'),
        ], 'wise-config');

        $this->app->make(Router::class)->aliasMiddleware('wise.verify-signature', VerifyWiseWebhookSignature::class);
    }

    /**
     * @phpstan-return WiseConfig
     */
    private function wiseConfig(Application $app): array
    {
        /** @phpstan-var WiseConfig $config */
        $config = $app->make('config')->get('wise');

        return $config;
    }

    /**
     * @phpstan-param  WiseConfig  $wiseConfig
     * @phpstan-param  array{client_id: string|null, client_secret: string|null}  $credentials
     */
    private function makeOAuthTokenClient(Application $app, array $wiseConfig, array $credentials): OAuthTokenClient
    {
        $baseUrl = rtrim($wiseConfig['servers'][$wiseConfig['environment']], '/');

        return new OAuthTokenClient(
            $app->make(HttpFactory::class),
            $baseUrl,
            Caster::string($credentials['client_id']),
            Caster::string($credentials['client_secret']),
        );
    }

    /**
     * @phpstan-param  WiseConfig  $config
     */
    private function makeUserTokenAuthenticator(Application $app, array $config): UserTokenAuthenticator
    {
        $userTokenConfig = $config['auth']['user_token'];
        $baseUrl = rtrim($config['servers'][$config['environment']], '/');
        $repositoryKey = $userTokenConfig['token_repository'] ?? TokenRepositoryInterface::class;

        if (! $app->bound($repositoryKey)) {
            throw new RuntimeException(
                'No TokenRepositoryInterface binding found. Bind one in your AppServiceProvider before using the user_token driver.'
            );
        }

        $repository = $app->make($repositoryKey);

        if (! $repository instanceof TokenRepositoryInterface) {
            throw new RuntimeException("The binding for [{$repositoryKey}] must implement TokenRepositoryInterface.");
        }

        return new UserTokenAuthenticator(
            $this->makeOAuthTokenClient($app, $config, [
                'client_id' => $userTokenConfig['client_id'],
                'client_secret' => $userTokenConfig['client_secret'],
            ]),
            $repository,
            "{$baseUrl}/oauth/authorize",
            Caster::string($userTokenConfig['client_id']),
            Caster::string($userTokenConfig['redirect_uri']),
        );
    }
}
