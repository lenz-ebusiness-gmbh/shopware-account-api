<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /plugins/testinginstances/config — global testing instances configuration.
 */
final class PluginTestingInstancesConfigRequest extends AbstractRequest
{
    public function path(): string
    {
        return '/plugins/testinginstances/config';
    }
}
