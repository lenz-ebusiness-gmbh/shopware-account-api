<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /plugins/{pluginId}/comments — customer comments/reviews of a single plugin.
 *
 * Distinct from PluginReviewsRequest (/plugins/{id}/reviews) and
 * PluginCommentListRequest (producer-wide /plugincomments).
 */
final class PluginCommentsRequest extends AbstractRequest
{
    public function __construct(private readonly int $pluginId)
    {
    }

    public function path(): string
    {
        return "/plugins/{$this->pluginId}/comments";
    }
}
