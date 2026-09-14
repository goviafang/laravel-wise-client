<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http;

use Govia\WiseClient\Exceptions\WiseException;

/**
 * Adapter over Laravel's Http facade. Commands/Queries only know this interface,
 * so swapping the transport (test doubles, a future client) never touches them.
 */
interface HttpClientInterface
{
    /**
     * @param  array<string, mixed>  $query
     *
     * @throws WiseException
     */
    public function get(string $uri, array $query = []): Response;

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     *
     * @throws WiseException
     */
    public function post(string $uri, array $body = [], array $headers = []): Response;

    /**
     * @param  array<string, mixed>  $body
     *
     * @throws WiseException
     */
    public function put(string $uri, array $body = []): Response;

    /**
     * @param  array<string, mixed>  $body
     *
     * @throws WiseException
     */
    public function patch(string $uri, array $body = []): Response;

    /**
     * @throws WiseException
     */
    public function delete(string $uri): Response;
}
