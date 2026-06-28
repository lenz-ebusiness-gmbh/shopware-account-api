<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request\Plugin;

use Lenz\ShopwareAccountApi\Enum\AbuseStatus;
use Lenz\ShopwareAccountApi\Request\AbstractListRequest;

/**
 * GET /pluginAbuses — abuse reports for a producer.
 *
 * Note: the producer query parameter is named "producer" (not "producerId").
 * Status accepts one or more values (AbuseStatus or string), sent as a comma-separated list.
 */
final class PluginAbuseListRequest extends AbstractListRequest
{
    /** @var list<string> */
    private array $status = [];

    public function __construct(private readonly int $producerId)
    {
        $this->orderBy = 'lastEncounter';
    }

    public function status(AbuseStatus|string ...$status): static
    {
        $this->status = array_map(
            static fn (AbuseStatus|string $s): string => $s instanceof AbuseStatus ? $s->value : $s,
            array_values($status),
        );

        return $this;
    }

    public function path(): string
    {
        return '/pluginAbuses';
    }

    protected function additionalQuery(): array
    {
        $query = ['producer' => $this->producerId];
        if ($this->status !== []) {
            $query['status'] = implode(',', $this->status);
        }

        return $query;
    }
}
