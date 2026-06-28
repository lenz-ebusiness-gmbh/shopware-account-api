<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Token;

/**
 * Keeps the token only for the lifetime of the current process.
 *
 * This is the default. The token is reused across requests within one run but
 * not persisted, so a new process re-authenticates.
 */
final class InMemoryTokenStorage implements TokenStorageInterface
{
    private ?Token $token = null;

    public function get(): ?Token
    {
        return $this->token;
    }

    public function save(Token $token): void
    {
        $this->token = $token;
    }

    public function clear(): void
    {
        $this->token = null;
    }
}
