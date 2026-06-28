<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Revenue;

use Lenz\ShopwareAccountApi\Request\AbstractRequest;

/**
 * GET /companies/{companyId}/kickbackbankdata — kickback bank data of a company.
 */
final class KickbackBankDataRequest extends AbstractRequest
{
    public function __construct(private readonly int $companyId)
    {
    }

    public function path(): string
    {
        return "/companies/{$this->companyId}/kickbackbankdata";
    }
}
