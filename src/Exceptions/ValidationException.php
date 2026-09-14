<?php

declare(strict_types=1);

namespace Govia\WiseClient\Exceptions;

use Govia\WiseClient\Support\Caster;

class ValidationException extends WiseException
{
    /**
     * @return array<int, array<array-key, mixed>>
     */
    public function errors(): array
    {
        return Caster::listOfArrays($this->body['errors'] ?? null);
    }
}
