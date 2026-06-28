<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Enum;

/**
 * Variant type for the producer sales endpoint (variantType query parameter).
 */
enum SalesVariantType: string
{
    case Buy = 'buy';
    case Rent = 'rent';
    case Free = 'free';
    case Test = 'test';
    case Support = 'support';
    case Abuses = 'abuses';
    case ProducerLicensed = 'producerLicensed';
}
