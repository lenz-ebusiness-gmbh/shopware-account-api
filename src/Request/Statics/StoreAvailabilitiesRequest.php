<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Statics;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /pluginstatics/storeAvailabilities — available store availabilities.
 */
final class StoreAvailabilitiesRequest extends AbstractRequest
{
    public function path(): string
    {
        return '/pluginstatics/storeAvailabilities';
    }
}
