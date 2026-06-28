<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/pluginwithdrawalrequests — plugin withdrawal requests.
 *
 * Optionally filtered by status, e.g. ->status('FilledIn').
 */
final class PluginWithdrawalRequestListRequest extends AbstractListRequest
{
    private ?string $status = null;

    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'id';
    }

    public function status(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/pluginwithdrawalrequests";
    }

    protected function additionalQuery(): array
    {
        return $this->status !== null ? ['status' => $this->status] : [];
    }
}
