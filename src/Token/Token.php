<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Token;

/**
 * Immutable value object holding a JWT and its expiry.
 */
final class Token
{
    public function __construct(
        public readonly string $value,
        public readonly \DateTimeImmutable $expiresAt,
    ) {
    }

    /**
     * Returns true while the token is still valid at the given (or current) time.
     */
    public function isValid(?\DateTimeImmutable $now = null): bool
    {
        return ($now ?? new \DateTimeImmutable()) < $this->expiresAt;
    }
}
