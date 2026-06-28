<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Revenue;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /producers/{producerId}/extensionpartnerrevenuebalance — outstanding revenue balance.
 *
 * Returns e.g. {"value": ...}. Optionally scoped by disbursal status
 * (e.g. ->disbursalStatus('undisbursed')).
 */
final class ExtensionPartnerRevenueBalanceRequest extends AbstractRequest
{
    private ?string $disbursalStatus = null;

    public function __construct(private readonly int $producerId)
    {
    }

    public function disbursalStatus(?string $disbursalStatus): static
    {
        $this->disbursalStatus = $disbursalStatus;

        return $this;
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/extensionpartnerrevenuebalance";
    }

    public function query(): array
    {
        return $this->disbursalStatus !== null ? ['disbursalStatus' => $this->disbursalStatus] : [];
    }
}
