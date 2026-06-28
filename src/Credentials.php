<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi;

/**
 * Immutable login credentials for the Shopware Account API.
 *
 * Only authentication data belongs here. Account context such as the producer
 * or company id is NOT a credential — it is passed to the client separately and
 * only required by the endpoints that actually use it.
 */
final class Credentials
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {
    }
}
