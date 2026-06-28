<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\InAppFeature;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /producers/{producerId}/inappfeatures/{featureId} — a single in-app feature.
 */
final class InAppFeatureDetailRequest extends AbstractRequest
{
    public function __construct(
        private readonly int $producerId,
        private readonly int $featureId,
    ) {
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/inappfeatures/{$this->featureId}";
    }
}
