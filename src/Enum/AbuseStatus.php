<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Enum;

/**
 * Status values for the plugin abuses endpoint.
 */
enum AbuseStatus: string
{
    case Open = 'open';
    case FirstReminder = 'first_reminder';
    case SecondReminder = 'second_reminder';
    case ThirdReminder = 'third_reminder';
    case OnHold = 'onHold';
    case FupAbuse = 'FUPAbuse';
}
