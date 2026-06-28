<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Sales;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/sales/priceadjustments — sales price adjustments.
 *
 * Optionally filtered by merchant acceptance status, e.g. ->merchantAcceptanceStatus('all').
 */
final class SalesPriceAdjustmentListRequest extends AbstractListRequest
{
    private ?string $merchantAcceptanceStatus = null;

    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'id';
        $this->orderSequence = 'asc';
    }

    public function merchantAcceptanceStatus(?string $status): static
    {
        $this->merchantAcceptanceStatus = $status;

        return $this;
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/sales/priceadjustments";
    }

    protected function additionalQuery(): array
    {
        return $this->merchantAcceptanceStatus !== null
            ? ['merchantAcceptanceStatus' => $this->merchantAcceptanceStatus]
            : [];
    }
}
