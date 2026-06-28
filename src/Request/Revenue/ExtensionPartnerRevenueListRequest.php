<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Revenue;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/extensionpartnerrevenues — partner revenues (provisions).
 *
 * The total count is returned in the 'sw-meta-total' response header; use
 * ShopwareAccountClient::sendRaw() to read it.
 */
final class ExtensionPartnerRevenueListRequest extends AbstractListRequest
{
    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'bookingDate';
        $this->orderSequence = 'asc';
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/extensionpartnerrevenues";
    }
}
