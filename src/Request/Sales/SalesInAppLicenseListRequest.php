<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Sales;

use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /producers/{producerId}/sales/inapplicenses — in-app purchase licenses.
 *
 * Optionally filtered by type (e.g. ->type('rental')) and whether they are
 * included in a plugin license (->includedInPluginLicense(false)).
 */
final class SalesInAppLicenseListRequest extends AbstractListRequest
{
    private ?string $type = null;
    private ?bool $includedInPluginLicense = null;

    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'id';
    }

    public function type(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function includedInPluginLicense(bool $value = true): static
    {
        $this->includedInPluginLicense = $value;

        return $this;
    }

    public function path(): string
    {
        return "/producers/{$this->producerId}/sales/inapplicenses";
    }

    protected function additionalQuery(): array
    {
        $query = [];
        if ($this->type !== null) {
            $query['type'] = $this->type;
        }
        if ($this->includedInPluginLicense !== null) {
            $query['includedInPluginLicense'] = $this->includedInPluginLicense;
        }

        return $query;
    }
}
