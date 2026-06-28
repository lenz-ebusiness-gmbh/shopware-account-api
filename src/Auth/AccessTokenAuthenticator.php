<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Auth;

use Lenz\ShopwareAccountApi\Exception\AuthenticationException;
use Lenz\ShopwareAccountApi\Http\HttpTransport;
use Lenz\ShopwareAccountApi\Token\Token;

/**
 * Default authenticator: POST /accesstokens {shopwareId, password} -> {token}.
 *
 * This is the simple, widely-used account login. The token expiry is taken from
 * the JWT exp claim when present, otherwise the configured fallback lifetime.
 */
final class AccessTokenAuthenticator implements AuthenticatorInterface
{
    private const URL = 'https://api.shopware.com/accesstokens';

    public function __construct(
        private readonly HttpTransport $transport,
        private readonly int $fallbackTtlMinutes = 50,
    ) {
    }

    public function login(string $username, string $password): Token
    {
        $response = $this->transport->send('POST', self::URL, ['Accept' => 'application/json'], [
            'shopwareId' => $username,
            'password'   => $password,
        ]);

        if (!$response->isSuccessful()) {
            throw new AuthenticationException(
                "Shopware account login failed (HTTP {$response->status}). Check Shopware ID and password.",
            );
        }

        $token = $response->json()['token'] ?? null;
        if (!is_string($token) || $token === '') {
            throw new AuthenticationException('Login did not return a token.');
        }

        return new Token($token, Jwt::expiry($token, $this->fallbackTtlMinutes));
    }
}
