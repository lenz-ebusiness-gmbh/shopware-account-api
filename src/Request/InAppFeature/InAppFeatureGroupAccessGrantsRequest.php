<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\InAppFeature;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/inappfeaturegroups/{groupId}/accessgrants — access grants of a group.
 */
final class InAppFeatureGroupAccessGrantsRequest extends AbstractListRequest
{
    public function __construct(
        private readonly int $producerId,
        private readonly int $groupId,
    ) {
        $this->orderBy = 'creationDate';
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/inappfeaturegroups/{$this->groupId}/accessgrants";
    }
}
