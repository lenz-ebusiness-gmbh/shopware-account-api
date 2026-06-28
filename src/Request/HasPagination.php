<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Request;

use Lenz\ShopwareAccountApi\Enum\OrderSequence;

/**
 * Fluent pagination / ordering / search options shared by list endpoints.
 */
trait HasPagination
{
    protected int $limit = 100;
    protected int $offset = 0;
    protected ?string $orderBy = null;
    protected string $orderSequence = 'desc';
    protected string $search = '';

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function orderBy(string $field, OrderSequence|string $sequence = OrderSequence::Desc): static
    {
        $this->orderBy = $field;
        $this->orderSequence = $sequence instanceof OrderSequence ? $sequence->value : $sequence;

        return $this;
    }

    public function search(string $search): static
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function paginationQuery(): array
    {
        $query = [
            'limit'         => $this->limit,
            'offset'        => $this->offset,
            'orderSequence' => $this->orderSequence,
            'search'        => $this->search,
        ];
        if ($this->orderBy !== null) {
            $query['orderBy'] = $this->orderBy;
        }

        return $query;
    }
}
