<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /producers/{producerId}/plugins/{pluginId}/binaries — a plugin's binaries.
 */
final class PluginBinariesRequest extends AbstractRequest
{
    public function __construct(
        private readonly int $producerId,
        private readonly int $pluginId,
    ) {
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/plugins/{$this->pluginId}/binaries";
    }
}
