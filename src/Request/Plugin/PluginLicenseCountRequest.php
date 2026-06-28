<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /plugins/{pluginId}/priceadjustment/licensecount — license count for a price adjustment.
 */
final class PluginLicenseCountRequest extends AbstractRequest
{
    public function __construct(private readonly int $pluginId)
    {
    }

    public function path(): string
    {
        return "/plugins/{$this->pluginId}/priceadjustment/licensecount";
    }
}
