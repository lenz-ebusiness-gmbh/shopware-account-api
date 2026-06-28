<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request;

/**
 * Base class for a plain (non-paginated) GET request.
 */
abstract class AbstractRequest implements AccountRequest
{
    public function method(): string
    {
        return 'GET';
    }

    public function query(): array
    {
        return [];
    }

    abstract public function path(): string;
}
