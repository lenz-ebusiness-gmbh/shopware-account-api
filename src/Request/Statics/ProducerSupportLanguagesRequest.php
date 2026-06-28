<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Statics;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /producersupportlanguages — supported producer support languages.
 */
final class ProducerSupportLanguagesRequest extends AbstractRequest
{
    public function path(): string
    {
        return '/producersupportlanguages';
    }
}
