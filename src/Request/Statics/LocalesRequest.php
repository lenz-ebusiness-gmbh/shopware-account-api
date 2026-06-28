<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Statics;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /pluginstatics/locales — available locales.
 */
final class LocalesRequest extends AbstractRequest
{
    public function path(): string
    {
        return '/pluginstatics/locales';
    }
}
