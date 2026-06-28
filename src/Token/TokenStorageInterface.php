<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Token;

/**
 * Persists the authentication token between requests.
 *
 * Implement this to cache the JWT wherever it fits your application (database,
 * cache, file, ...). A persistent implementation avoids re-authenticating on
 * every CLI run or web request. See InMemoryTokenStorage / FileTokenStorage.
 */
interface TokenStorageInterface
{
    /**
     * Returns the stored token, or null if none is stored.
     */
    public function get(): ?Token;

    /**
     * Stores (or replaces) the token.
     */
    public function save(Token $token): void;

    /**
     * Removes any stored token (e.g. after a 401 response).
     */
    public function clear(): void;
}
