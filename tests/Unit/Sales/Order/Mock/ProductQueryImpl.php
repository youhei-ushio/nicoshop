<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Application\Persistence\ProductPaginator;
use App\Contexts\Sales\Application\Persistence\ProductQuery;
use App\Contexts\Sales\Domain\Value\Product;
use ArrayIterator;
use RuntimeException;

final class ProductQueryImpl implements ProductQuery
{
    private array $filterIds = [];
    private int $perPage = 1000;
    private int $currentPage = 1;

    public function __construct(private readonly array $items)
    {

    }

    public function filterByIds(array $ids): ProductQuery
    {
        $this->filterIds = $ids;
        return $this;
    }

    public function paginate(int $perPage, int $currentPage): ProductQuery
    {
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        return $this;
    }

    public function get(): ProductPaginator
    {
        return new class(
            $this->items,
            $this->filterIds,
            $this->perPage,
            $this->currentPage,
        ) extends ArrayIterator implements ProductPaginator
        {
            public function __construct(
                private readonly array $items,
                private readonly array $filterIds,
                private readonly int $perPage,
                private readonly int $currentPage,
            )
            {
                parent::__construct(
                    array_slice(
                        array_filter($items, function (Product $product) use ($filterIds) {
                            return in_array($product->id, $this->filterIds, true);
                        }),
                        $perPage * ($currentPage - 1),
                        $perPage
                    )
                );
            }

            public function current(): Product
            {
                return parent::current();
            }

            public function total(): int
            {
                return count($this->items);
            }

            public function perPage(): int
            {
                return $this->perPage;
            }

            public function currentPage(): int
            {
                return $this->currentPage;
            }

            public function getById(int $id): Product
            {
                foreach ($this as $product) {
                    if ($product->id === $id) {
                        return $product;
                    }
                }
                throw new RuntimeException('not found');
            }
        };
    }
}