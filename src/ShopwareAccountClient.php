<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Lenz\ShopwareAccountApi\Auth\AccessTokenAuthenticator;
use Lenz\ShopwareAccountApi\Auth\AuthenticatorInterface;
use Lenz\ShopwareAccountApi\Exception\ApiException;
use Lenz\ShopwareAccountApi\Http\HttpTransport;
use Lenz\ShopwareAccountApi\Http\Response;
use Lenz\ShopwareAccountApi\Request\AbstractListRequest;
use Lenz\ShopwareAccountApi\Request\AccountRequest;
use Lenz\ShopwareAccountApi\Token\InMemoryTokenStorage;
use Lenz\ShopwareAccountApi\Token\TokenStorageInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Standalone client for the Shopware Account / Store (SBP) API.
 *
 * The client handles authentication (the token is cached via the configured
 * TokenStorageInterface) and transport. Endpoints are expressed as request
 * objects (see the Request namespace) and executed with send() / sendRaw();
 * account context such as producerId/companyId lives on those request objects.
 *
 * Authentication defaults to AccessTokenAuthenticator (POST /accesstokens); pass
 * a KratosAuthenticator (or any AuthenticatorInterface) to override it. The HTTP
 * client and PSR-17 factories are auto-discovered when not provided, so any
 * PSR-18 implementation (Guzzle, Symfony HttpClient, ...) works.
 */
final class ShopwareAccountClient
{
    private const API_BASE_URL = 'https://api.shopware.com';

    private readonly HttpTransport $transport;
    private readonly AuthenticatorInterface $authenticator;
    private readonly TokenStorageInterface $tokenStorage;

    public function __construct(
        private readonly Credentials $credentials,
        ?TokenStorageInterface $tokenStorage = null,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        int $tokenTtlMinutes = 50,
        ?AuthenticatorInterface $authenticator = null,
    ) {
        $this->transport = new HttpTransport(
            $httpClient ?? Psr18ClientDiscovery::find(),
            $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory(),
            $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory(),
        );
        $this->authenticator = $authenticator ?? new AccessTokenAuthenticator($this->transport, $tokenTtlMinutes);
        $this->tokenStorage  = $tokenStorage ?? new InMemoryTokenStorage();
    }

    // -------------------------------------------------------------------------
    // Request execution
    // -------------------------------------------------------------------------

    /**
     * Executes a request and returns the decoded JSON body.
     *
     * @return array<int|string, mixed>
     */
    public function send(AccountRequest $request): array
    {
        return $this->dispatch($request)->json();
    }

    /**
     * Executes a request and returns the raw response (status, headers, body).
     *
     * Use this when you need response headers, e.g. the 'sw-meta-total' paging
     * header on list endpoints.
     */
    public function sendRaw(AccountRequest $request): Response
    {
        return $this->dispatch($request);
    }

    /**
     * Iterates a list endpoint across all pages, yielding each item.
     *
     * The request's limit/offset are managed automatically; assumes the endpoint
     * returns a top-level JSON array (as the account list endpoints do).
     *
     * @return iterable<int, mixed>
     */
    public function paginate(AbstractListRequest $request, int $pageSize = 100): iterable
    {
        $offset = 0;
        do {
            $request->limit($pageSize)->offset($offset);
            $page = $this->send($request);
            foreach ($page as $item) {
                yield $item;
            }
            $offset += $pageSize;
        } while (\count($page) === $pageSize);
    }

    /**
     * Downloads the binary content of an attachment from its signed (S3) URL.
     *
     * This is not a regular account-API request (the URL is pre-signed and
     * unauthenticated), so it is not modelled as a request object.
     *
     * @return array{content: string, mimeType: string, size: int}
     */
    public function downloadAttachment(string $remoteLink): array
    {
        $response = $this->transport->send('GET', $remoteLink);
        if (!$response->isSuccessful()) {
            throw new ApiException("Attachment download failed (HTTP {$response->status}).", $response->status);
        }

        $mimeType = $response->header('content-type') ?? 'application/octet-stream';
        if (str_contains($mimeType, ';')) {
            $mimeType = trim(explode(';', $mimeType)[0]);
        }

        return [
            'content'  => $response->body,
            'mimeType' => $mimeType,
            'size'     => \strlen($response->body),
        ];
    }

    // -------------------------------------------------------------------------
    // Authentication
    // -------------------------------------------------------------------------

    /**
     * Returns a valid token, authenticating (and caching it) when needed.
     */
    public function getToken(): string
    {
        $token = $this->tokenStorage->get();
        if ($token !== null && $token->isValid()) {
            return $token->value;
        }
        $token = $this->authenticator->login($this->credentials->username, $this->credentials->password);
        $this->tokenStorage->save($token);

        return $token->value;
    }

    /**
     * Drops the cached token so the next call re-authenticates.
     */
    public function invalidateToken(): void
    {
        $this->tokenStorage->clear();
    }

    // -------------------------------------------------------------------------
    // Internal
    // -------------------------------------------------------------------------

    /**
     * Performs the authenticated request, re-authenticating and retrying once on 401.
     */
    private function dispatch(AccountRequest $request): Response
    {
        $url = self::API_BASE_URL . $request->path();

        $response = $this->transport->send($request->method(), $url, $this->authHeaders(), null, $request->query());
        if ($response->status === 401) {
            $this->invalidateToken();
            $response = $this->transport->send($request->method(), $url, $this->authHeaders(), null, $request->query());
        }
        if (!$response->isSuccessful()) {
            throw $this->error($request->method(), $url, $response);
        }

        return $response;
    }

    /**
     * Builds an ApiException, surfacing the Shopware error body ({success, code, detail}).
     */
    private function error(string $method, string $url, Response $response): ApiException
    {
        $message = sprintf('%s %s failed with HTTP %d', $method, $url, $response->status);

        $data = json_decode($response->body, true);
        if (is_array($data)) {
            $code = $data['code'] ?? null;
            $detail = $data['detail'] ?? null;
            if (is_scalar($code)) {
                $message .= sprintf(' (code %s)', (string) $code);
            }
            if (is_string($detail) && $detail !== '') {
                $message .= ': ' . $detail;
            }
        }

        return new ApiException($message . '.', $response->status, $response->body);
    }

    /**
     * @return array<string, string>
     */
    private function authHeaders(): array
    {
        return [
            'X-Shopware-Token' => $this->getToken(),
            'Accept'           => 'application/json',
        ];
    }
}
