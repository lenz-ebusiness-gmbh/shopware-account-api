<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Partner;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /partners/{partnerId} — partner detail.
 */
final class PartnerDetailRequest extends AbstractRequest
{
    public function __construct(private readonly int $partnerId)
    {
    }

    public function path(): string
    {
        return "/partners/{$this->partnerId}";
    }
}
