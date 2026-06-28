<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Statics;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;
use Lenz\ShopwareAccountApi\Request\HasFilters;

/**
 * GET /pluginstatics/all — combined plugin static data.
 *
 * Filterable, e.g. ->where('pluginGeneration', 'apps')->where('includeNonPublic', '1').
 */
final class PluginStaticsAllRequest extends AbstractRequest
{
    use HasFilters;

    public function path(): string
    {
        return '/pluginstatics/all';
    }

    public function query(): array
    {
        return $this->filterQuery();
    }
}
