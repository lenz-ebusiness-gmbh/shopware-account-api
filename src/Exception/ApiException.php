<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Exception;

/**
 * Thrown when an API request fails (transport error or non-2xx HTTP status).
 */
class ApiException extends ShopwareAccountApiException
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
        public readonly ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
