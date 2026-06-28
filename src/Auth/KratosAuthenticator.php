<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Auth;

use Lenz\ShopwareAccountApi\Exception\AuthenticationException;
use Lenz\ShopwareAccountApi\Http\HttpTransport;
use Lenz\ShopwareAccountApi\Token\Token;

/**
 * Alternative authenticator using the Ory Kratos login flow on auth-api.shopware.com.
 *
 * Flow:
 *   1. GET the login flow to obtain the action URL.
 *   2. POST the credentials to that action URL to obtain a session token.
 *   3. GET whoami with the session token to obtain the tokenized JWT.
 */
final class KratosAuthenticator implements AuthenticatorInterface
{
    private const FLOW_URL   = 'https://auth-api.shopware.com/self-service/login/api';
    private const WHOAMI_URL = 'https://auth-api.shopware.com/sessions/whoami?tokenize_as=shopware_account';

    public function __construct(
        private readonly HttpTransport $transport,
        private readonly int $fallbackTtlMinutes = 50,
    ) {
    }

    public function login(string $username, string $password): Token
    {
        // Step 1: initialise the login flow.
        $flow = $this->transport->send('GET', self::FLOW_URL, ['Accept' => 'application/json']);
        if (!$flow->isSuccessful()) {
            throw new AuthenticationException("Could not start login flow (HTTP {$flow->status}).");
        }
        $ui = $flow->json()['ui'] ?? null;
        $actionUrl = is_array($ui) ? ($ui['action'] ?? null) : null;
        if (!is_string($actionUrl) || $actionUrl === '') {
            throw new AuthenticationException('Login flow did not return an action URL.');
        }

        // Step 2: submit credentials.
        $login = $this->transport->send('POST', $actionUrl, ['Accept' => 'application/json'], [
            'identifier' => $username,
            'password'   => $password,
            'method'     => 'password',
        ]);
        if (!$login->isSuccessful()) {
            throw new AuthenticationException("Login failed (HTTP {$login->status}). Check Shopware ID and password.");
        }
        $sessionToken = $login->json()['session_token'] ?? null;
        if (!is_string($sessionToken) || $sessionToken === '') {
            throw new AuthenticationException('Login did not return a session token.');
        }

        // Step 3: exchange the session token for a JWT.
        $whoami = $this->transport->send('GET', self::WHOAMI_URL, [
            'Accept'          => 'application/json',
            'X-Session-Token' => $sessionToken,
        ]);
        if (!$whoami->isSuccessful()) {
            throw new AuthenticationException("Token exchange failed (HTTP {$whoami->status}).");
        }
        $jwt = $whoami->json()['tokenized'] ?? null;
        if (!is_string($jwt) || $jwt === '') {
            throw new AuthenticationException('whoami did not return a tokenized JWT.');
        }

        return new Token($jwt, Jwt::expiry($jwt, $this->fallbackTtlMinutes));
    }
}
