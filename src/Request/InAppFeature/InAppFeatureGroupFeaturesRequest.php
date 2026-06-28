<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\InAppFeature;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/inappfeaturegroups/{groupId}/features — features of a group.
 */
final class InAppFeatureGroupFeaturesRequest extends AbstractListRequest
{
    public function __construct(
        private readonly int $producerId,
        private readonly int $groupId,
    ) {
        $this->orderBy = 'id';
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/inappfeaturegroups/{$this->groupId}/features";
    }
}
