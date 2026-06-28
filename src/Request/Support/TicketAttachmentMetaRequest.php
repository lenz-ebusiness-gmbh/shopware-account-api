<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Support;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /companies/{companyId}/supporttickets/{ticketId}/attachments/{attachmentId}
 * — signed download metadata for a ticket attachment.
 *
 * The returned remoteLink can be passed to ShopwareAccountClient::downloadAttachment().
 */
final class TicketAttachmentMetaRequest extends AbstractRequest
{
    public function __construct(
        private readonly int $companyId,
        private readonly int $ticketId,
        private readonly int $attachmentId,
    ) {
    }

    public function path(): string
    {
        return "/companies/{$this->companyId}/supporttickets/{$this->ticketId}/attachments/{$this->attachmentId}";
    }

    public function query(): array
    {
        return ['json' => 'true'];
    }
}
