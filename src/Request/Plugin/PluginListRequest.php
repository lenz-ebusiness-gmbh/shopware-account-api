<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;
use Lenz\ShopwareAccountApi\Request\HasFilters;

/**
 * GET /plugins — a producer's plugins/extensions.
 *
 * Supports filters, e.g. ->where('generation', 'platform') or
 * ->where('isCompatible', false)->where('shopwareVersion', 5).
 */
final class PluginListRequest extends AbstractListRequest
{
    use HasFilters;

    private bool $simpleData = false;

    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'id';
    }

    public function simpleData(bool $simpleData = true): static
    {
        $this->simpleData = $simpleData;

        return $this;
    }

    public function path(): string
    {
        return '/plugins';
    }

    protected function additionalQuery(): array
    {
        $query = ['producerId' => $this->producerId];
        if ($this->simpleData) {
            $query['simpleData'] = true;
        }

        return array_merge($query, $this->filterQuery());
    }
}
