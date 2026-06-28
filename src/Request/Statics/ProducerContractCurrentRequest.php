<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Statics;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /documents/producerContract/versions/current — current producer contract version.
 */
final class ProducerContractCurrentRequest extends AbstractRequest
{
    public function path(): string
    {
        return '/documents/producerContract/versions/current';
    }
}
