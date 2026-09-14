<?php

declare(strict_types=1);

namespace Govia\WiseClient\Exceptions;

use Exception;

/**
 * Wise requires a Strong Customer Authentication challenge before this action can proceed.
 * This is a business-level exception, not a transport error, so it does not extend WiseException.
 */
class ScaChallengeRequiredException extends Exception
{
    /**
     * @param  array<int, string>  $availableMethods  verification methods Wise offers, e.g. ['otp', 'pin']
     */
    public function __construct(
        private readonly string $oneTimeToken,
        private readonly array $availableMethods = [],
    ) {
        parent::__construct('Wise requires strong customer authentication before this action can proceed.');
    }

    public function oneTimeToken(): string
    {
        return $this->oneTimeToken;
    }

    /**
     * @return array<int, string>
     */
    public function availableMethods(): array
    {
        return $this->availableMethods;
    }
}
