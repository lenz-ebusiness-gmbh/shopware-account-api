<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /plugincomments/{commentId} — a single plugin comment/review.
 */
final class PluginCommentDetailRequest extends AbstractRequest
{
    public function __construct(private readonly int $commentId)
    {
    }

    public function path(): string
    {
        return "/plugincomments/{$this->commentId}";
    }
}
