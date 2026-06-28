<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Revenue;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /producers/{producerId}/extensionpartnerrevenues/taxinformation — tax information for revenues.
 */
final class ExtensionPartnerRevenueTaxInfoRequest extends AbstractRequest
{
    public function __construct(private readonly int $producerId)
    {
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/extensionpartnerrevenues/taxinformation";
    }
}
