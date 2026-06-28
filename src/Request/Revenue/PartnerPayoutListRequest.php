<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Revenue;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /companies/{companyId}/partnerpayouts — partner payouts of a company.
 *
 * Optionally scoped by context, e.g. ->context('extensionPartnerRevenue').
 */
final class PartnerPayoutListRequest extends AbstractListRequest
{
    private ?string $context = null;

    public function __construct(private readonly int $companyId)
    {
        $this->orderBy = 'billingDate';
    }

    public function context(?string $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function path(): string
    {
        return "/companies/{$this->companyId}/partnerpayouts";
    }

    protected function additionalQuery(): array
    {
        return $this->context !== null ? ['context' => $this->context] : [];
    }
}
