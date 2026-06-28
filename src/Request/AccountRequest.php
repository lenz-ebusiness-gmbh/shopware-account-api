<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request;

/**
 * A single API request, described by its HTTP method, path and query parameters.
 *
 * Each endpoint is represented by its own request class so its options are
 * configured on the object (fluent setters) instead of via long method
 * signatures. Execute it with ShopwareAccountClient::send() / sendRaw().
 */
interface AccountRequest
{
    public function method(): string;

    /**
     * Path relative to https://api.shopware.com, starting with a slash.
     */
    public function path(): string;

    /**
     * @return array<string, mixed> query parameters (booleans become true/false)
     */
    public function query(): array;
}
