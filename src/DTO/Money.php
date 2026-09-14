<?php

declare(strict_types=1);

namespace Govia\WiseClient\DTO;

final class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
    ) {}
}
