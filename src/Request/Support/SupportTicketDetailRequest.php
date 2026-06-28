<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Support;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /producers/{producerId}/supporttickets/{ticketId} — full ticket detail incl. answers.
 */
final class SupportTicketDetailRequest extends AbstractRequest
{
    public function __construct(
        private readonly int $producerId,
        private readonly int $ticketId,
    ) {
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/supporttickets/{$this->ticketId}";
    }
}
