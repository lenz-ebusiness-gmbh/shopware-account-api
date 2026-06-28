<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Statics;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;
use Lenz\ShopwareAccountApi\Request\HasFilters;

/**
 * GET /pluginstatics/softwareVersions — available software versions.
 *
 * Typically filtered by plugin generation, e.g.
 * ->where('pluginGeneration', 'classic') or ->where('pluginGeneration', 'platform').
 */
final class SoftwareVersionsRequest extends AbstractRequest
{
    use HasFilters;

    public function path(): string
    {
        return '/pluginstatics/softwareVersions';
    }

    public function query(): array
    {
        return $this->filterQuery();
    }
}
