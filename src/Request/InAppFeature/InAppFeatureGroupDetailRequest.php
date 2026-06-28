<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\InAppFeature;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /producers/{producerId}/inappfeaturegroups/{groupId} — a single feature group.
 */
final class InAppFeatureGroupDetailRequest extends AbstractRequest
{
    public function __construct(
        private readonly int $producerId,
        private readonly int $groupId,
    ) {
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/inappfeaturegroups/{$this->groupId}";
    }
}
