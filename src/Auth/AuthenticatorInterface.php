<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Auth;

use Lenz\ShopwareAccountApi\Token\Token;

/**
 * Authenticates against the Shopware account API and returns a token.
 *
 * Default implementation: AccessTokenAuthenticator (POST /accesstokens).
 * Alternative: KratosAuthenticator (auth-api.shopware.com login flow).
 */
interface AuthenticatorInterface
{
    public function login(string $username, string $password): Token;
}
