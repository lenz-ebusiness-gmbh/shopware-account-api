<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request;

/**
 * Base class for paginated list endpoints (limit/offset/orderBy/orderSequence/search).
 *
 * Subclasses provide the path and may add endpoint-specific parameters by
 * overriding additionalQuery() (e.g. producerId, status, variantType, filters).
 */
abstract class AbstractListRequest extends AbstractRequest
{
    use HasPagination;

    public function query(): array
    {
        return array_merge($this->paginationQuery(), $this->additionalQuery());
    }

    /**
     * @return array<string, mixed>
     */
    protected function additionalQuery(): array
    {
        return [];
    }
}
