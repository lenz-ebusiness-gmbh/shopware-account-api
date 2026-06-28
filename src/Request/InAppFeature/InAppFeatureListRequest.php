<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\InAppFeature;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/inappfeatures — a producer's in-app features.
 */
final class InAppFeatureListRequest extends AbstractListRequest
{
    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'id';
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/inappfeatures";
    }
}
