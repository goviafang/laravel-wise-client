<?php

declare(strict_types=1);

namespace Govia\WiseClient\Auth;

interface AuthenticatorInterface
{
    /**
     * Returns the bearer token for this request. Implementations handle their own renewal.
     */
    public function token(): string;
}
