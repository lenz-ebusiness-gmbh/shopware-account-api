<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\InAppFeature;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/inappfeaturegroups — a producer's in-app feature groups.
 */
final class InAppFeatureGroupListRequest extends AbstractListRequest
{
    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'id';
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/inappfeaturegroups";
    }
}
