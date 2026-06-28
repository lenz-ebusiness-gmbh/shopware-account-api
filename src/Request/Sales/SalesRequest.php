<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Sales;

use Lenz\ShopwareAccountApi\Enum\SalesVariantType;
use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/sales — a producer's sales.
 *
 * The variant type selects what is listed, e.g. ->variantType(SalesVariantType::Buy).
 * Optional flags map to the corresponding query parameters.
 */
final class SalesRequest extends AbstractListRequest
{
    private ?string $variantType = null;
    private ?bool $includeTrialInTest = null;
    private ?bool $onlyWithActiveCancellationOffer = null;
    private ?bool $onlyWithSubscriptions = null;

    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'creationDate';
    }

    public function variantType(SalesVariantType|string $variantType): static
    {
        $this->variantType = $variantType instanceof SalesVariantType ? $variantType->value : $variantType;

        return $this;
    }

    public function includeTrialInTest(bool $value = true): static
    {
        $this->includeTrialInTest = $value;

        return $this;
    }

    public function onlyWithActiveCancellationOffer(bool $value = true): static
    {
        $this->onlyWithActiveCancellationOffer = $value;

        return $this;
    }

    public function onlyWithSubscriptions(bool $value = true): static
    {
        $this->onlyWithSubscriptions = $value;

        return $this;
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/sales";
    }

    protected function additionalQuery(): array
    {
        $query = [];
        if ($this->variantType !== null) {
            $query['variantType'] = $this->variantType;
        }
        if ($this->includeTrialInTest !== null) {
            $query['includeTrialInTest'] = $this->includeTrialInTest;
        }
        if ($this->onlyWithActiveCancellationOffer !== null) {
            $query['onlyWithActiveCancellationOffer'] = $this->onlyWithActiveCancellationOffer;
        }
        if ($this->onlyWithSubscriptions !== null) {
            $query['onlyWithSubscriptions'] = $this->onlyWithSubscriptions;
        }

        return $query;
    }
}
