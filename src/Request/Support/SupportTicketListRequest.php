<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Support;

use Lenz\ShopwareAccountApi\Enum\TicketStatus;
use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/supporttickets — a producer's support tickets.
 *
 * Optionally filtered by status, e.g. ->status(TicketStatus::Open).
 */
final class SupportTicketListRequest extends AbstractListRequest
{
    private ?string $status = null;

    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'lastContact';
    }

    public function status(TicketStatus|string|null $status): static
    {
        $this->status = $status instanceof TicketStatus ? $status->value : $status;

        return $this;
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/supporttickets";
    }

    protected function additionalQuery(): array
    {
        return $this->status !== null ? ['status' => $this->status] : [];
    }
}
