<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Http;

use Lenz\ShopwareAccountApi\Exception\ApiException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Thin wrapper around a PSR-18 client that builds requests, serialises JSON
 * bodies and normalises responses. Does NOT throw on non-2xx status codes —
 * callers inspect Response::$status (needed for the 401 re-auth retry).
 */
final class HttpTransport
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    /**
     * @param array<string, string>     $headers
     * @param array<string, mixed>|null $json    JSON request body (sets Content-Type)
     * @param array<string, mixed>      $query   query string parameters
     */
    public function send(
        string $method,
        string $url,
        array $headers = [],
        ?array $json = null,
        array $query = [],
    ): Response {
        if ($query !== []) {
            $normalized = [];
            foreach ($query as $key => $value) {
                $normalized[$key] = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            }
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($normalized);
        }

        $request = $this->requestFactory->createRequest($method, $url);

        if (!isset(array_change_key_case($headers)['accept'])) {
            $request = $request->withHeader('Accept', 'application/json');
        }
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($json !== null) {
            $body = json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->streamFactory->createStream($body));
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ApiException("HTTP transport error for $method $url: " . $e->getMessage(), 0, null, $e);
        }

        $headersOut = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headersOut[strtolower($name)] = array_values($values);
        }

        return new Response($response->getStatusCode(), $headersOut, (string) $response->getBody());
    }
}
