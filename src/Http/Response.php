<?php

declare(strict_types=1);

namespace Govia\WiseClient\Http;

final class Response
{
    /**
     * @param  array<array-key, mixed>  $json
     * @param  array<string, string>  $headers
     */
    public function __construct(
        public readonly int $status,
        public readonly array $json,
        public readonly array $headers,
    ) {}

    public function header(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }
}
