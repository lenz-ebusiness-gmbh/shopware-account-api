<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Enum;

/**
 * Plugin generation, used in filters (e.g. where('generation', PluginGeneration::Platform))
 * and in plugin-statics filters (pluginGeneration).
 *
 * These are the canonical generations. Combined filter values such as
 * "platform,apps,themes" are a UI grouping, not a generation — pass those as a
 * raw string to where() when needed.
 */
enum PluginGeneration: string
{
    case Classic = 'classic';
    case Platform = 'platform';
    case Apps = 'apps';
}
