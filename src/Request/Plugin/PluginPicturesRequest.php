<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /plugins/{pluginId}/pictures — a plugin's pictures.
 */
final class PluginPicturesRequest extends AbstractRequest
{
    public function __construct(private readonly int $pluginId)
    {
    }

    public function path(): string
    {
        return "/plugins/{$this->pluginId}/pictures";
    }
}
