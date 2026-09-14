<?php

declare(strict_types=1);

namespace Govia\WiseClient\Auth;

/**
 * Where OAuth2 (UserToken) tokens get stored is up to the host app.
 * Implement this and bind it in your service container; the package assumes no DB schema.
 */
interface TokenRepositoryInterface
{
    public function get(): ?AccessToken;

    public function put(AccessToken $token): void;
}
