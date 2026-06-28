<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Enum;

/**
 * Sort direction for list endpoints (orderSequence query parameter).
 */
enum OrderSequence: string
{
    case Asc = 'asc';
    case Desc = 'desc';
}
