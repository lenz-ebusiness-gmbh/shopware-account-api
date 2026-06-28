<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /plugincomments — comments/reviews for a producer's plugins.
 */
final class PluginCommentListRequest extends AbstractListRequest
{
    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'creationDate';
    }

    public function path(): string
    {
        return '/plugincomments';
    }

    protected function additionalQuery(): array
    {
        return ['producerId' => $this->producerId];
    }
}
