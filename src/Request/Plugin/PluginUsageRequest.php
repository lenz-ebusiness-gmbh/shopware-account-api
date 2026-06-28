<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /statistics/shopwareversiondistribution/{pluginId} — Shopware version
 * distribution (usage statistics) for a plugin.
 */
final class PluginUsageRequest extends AbstractRequest
{
    public function __construct(private readonly int $pluginId)
    {
    }

    public function path(): string
    {
        return "/statistics/shopwareversiondistribution/{$this->pluginId}";
    }
}
