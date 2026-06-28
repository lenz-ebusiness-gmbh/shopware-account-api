<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Statics;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /wiki/account/locale/{locale}/path/{path} — account wiki content for a path.
 *
 * Example: new WikiRequest('producer/support') or new WikiRequest('producer/reviews', 'en').
 */
final class WikiRequest extends AbstractRequest
{
    public function __construct(
        private readonly string $wikiPath,
        private readonly string $locale = 'de',
    ) {
    }

    public function path(): string
    {
        $segments = array_map('rawurlencode', explode('/', trim($this->wikiPath, '/')));

        return sprintf('/wiki/account/locale/%s/path/%s', rawurlencode($this->locale), implode('/', $segments));
    }
}
