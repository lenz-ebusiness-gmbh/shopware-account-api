<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Tests;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Minimal PSR-18 client that returns queued responses and records requests.
 */
final class MockClient implements ClientInterface
{
    /** @var list<ResponseInterface> */
    private array $queue;

    /** @var list<RequestInterface> */
    public array $requests = [];

    public function __construct(ResponseInterface ...$responses)
    {
        $this->queue = array_values($responses);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        if ($this->queue === []) {
            throw new \RuntimeException('MockClient: no more queued responses.');
        }

        return array_shift($this->queue);
    }

    public function lastRequest(): RequestInterface
    {
        if ($this->requests === []) {
            throw new \RuntimeException('MockClient: no requests recorded.');
        }

        return $this->requests[array_key_last($this->requests)];
    }
}
