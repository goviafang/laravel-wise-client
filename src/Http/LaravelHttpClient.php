<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http;

use Govia\WiseClient\Auth\AuthenticatorInterface;
use Govia\WiseClient\Exceptions\RateLimitExceededException;
use Govia\WiseClient\Exceptions\ScaChallengeRequiredException;
use Govia\WiseClient\Exceptions\WiseException;
use Govia\WiseClient\Support\Caster;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response as IlluminateResponse;

final class LaravelHttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly HttpFactory $http,
        private readonly AuthenticatorInterface $authenticator,
        private readonly string $baseUrl,
        private readonly int $timeout,
        private readonly bool $retryOnRateLimit,
        private readonly int $maxRetryAttempts,
    ) {}

    public function get(string $uri, array $query = []): Response
    {
        return $this->send('get', $uri, $query);
    }

    public function post(string $uri, array $body = [], array $headers = []): Response
    {
        return $this->send('post', $uri, $body, $headers);
    }

    public function put(string $uri, array $body = []): Response
    {
        return $this->send('put', $uri, $body);
    }

    public function patch(string $uri, array $body = []): Response
    {
        return $this->send('patch', $uri, $body);
    }

    public function delete(string $uri): Response
    {
        return $this->send('delete', $uri);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    private function send(string $method, string $uri, array $payload = [], array $headers = [], int $attempt = 1): Response
    {
        $request = $this->request()->withHeaders($headers);
        $url = "{$this->baseUrl}{$uri}";

        $raw = match ($method) {
            'get' => $request->get($url, $payload),
            'post' => $request->post($url, $payload),
            'put' => $request->put($url, $payload),
            'patch' => $request->patch($url, $payload),
            default => $request->delete($url, $payload),
        };

        if ($raw->status() === 429 && $this->retryOnRateLimit && $attempt <= $this->maxRetryAttempts) {
            sleep((int) ($raw->header('Retry-After') ?: 1));

            return $this->send($method, $uri, $payload, $headers, $attempt + 1);
        }

        return $this->toResponseOrThrow($raw);
    }

    private function request(): PendingRequest
    {
        return $this->http
            ->baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withToken($this->authenticator->token())
            ->acceptJson();
    }

    private function toResponseOrThrow(IlluminateResponse $raw): Response
    {
        $headers = $this->flattenHeaders($raw);
        $body = Caster::array($raw->json());

        if ($raw->status() === 429) {
            throw new RateLimitExceededException(
                'Wise API rate limit exceeded.',
                429,
                $body,
                isset($headers['Retry-After']) ? (int) $headers['Retry-After'] : null,
            );
        }

        // Wise's SCA challenge convention: 403 with an x-2fa-approval header carrying a one-time-token
        $twoFaHeader = $raw->header('x-2fa-approval');

        if ($raw->status() === 403 && $twoFaHeader !== '') {
            throw new ScaChallengeRequiredException(
                oneTimeToken: $twoFaHeader,
                availableMethods: Caster::stringList($body['availableMethods'] ?? null),
            );
        }

        if ($raw->failed()) {
            throw WiseException::fromResponse($raw->status(), $body);
        }

        return new Response($raw->status(), $body, $headers);
    }

    /**
     * @return array<string, string>
     */
    private function flattenHeaders(IlluminateResponse $raw): array
    {
        $headers = [];

        foreach ($raw->headers() as $name => $values) {
            $headers[(string) $name] = is_array($values) ? Caster::string($values[0] ?? null) : Caster::string($values);
        }

        return $headers;
    }
}
