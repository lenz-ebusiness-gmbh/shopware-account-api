<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request;

/**
 * Fluent support for the Shopware "filter" query parameter, a JSON-encoded
 * array of {property, value} pairs (e.g. filter=[{"property":"generation","value":"platform"}]).
 */
trait HasFilters
{
    /** @var list<array{property: string, value: mixed}> */
    protected array $filters = [];

    /**
     * Adds a filter criterion. Value may be a string, int, bool or array.
     */
    public function where(string $property, mixed $value): static
    {
        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }
        $this->filters[] = ['property' => $property, 'value' => $value];

        return $this;
    }

    /**
     * @return array<string, string>
     */
    protected function filterQuery(): array
    {
        if ($this->filters === []) {
            return [];
        }

        return [
            'filter' => json_encode(
                $this->filters,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            ),
        ];
    }
}
