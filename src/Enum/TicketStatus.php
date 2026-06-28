<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Enum;

/**
 * Support ticket status (status query parameter on the ticket list endpoint).
 */
enum TicketStatus: string
{
    case Open = 'open';
    case Answered = 'answered';
    case Closed = 'closed';
}
