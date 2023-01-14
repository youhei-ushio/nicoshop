<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Application\Persistence\ProductPaginator;
use App\Contexts\Sales\Application\Persistence\ProductQuery;
use App\Contexts\Sales\Application\Persistence\ProductQueryResult;
use App\Models;
use IteratorIterator;
use Traversable;

final class ProductQueryImpl implements ProductQuery
{
    private int $perPage = 1000;
    private int $currentPage = 1;

    public function paginate(int $perPage, int $currentPage): ProductQuery
    {
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        return $this;
    }

    public function get(): ProductPaginator
    {
        $paginator = Models\Product::query()
            ->paginate(perPage: $this->perPage, page: $this->currentPage);

        return new class(
            $paginator,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
        ) extends IteratorIterator implements ProductPaginator
        {
            public function __construct(
                Traversable $paginator,
                private readonly int $total,
                private readonly int $perPage,
                private readonly int $currentPage,
            )
            {
                parent::__construct($paginator);
            }

            public function current(): ProductQueryResult
            {
                /** @var Models\Product $row */
                $row = parent::current();
                return new ProductQueryResult(
                    $row->id,
                    $row->name,
                );
            }

            public function total(): int
            {
                return $this->total;
            }

            public function perPage(): int
            {
                return $this->perPage;
            }

            public function currentPage(): int
            {
                return $this->currentPage;
            }
        };
    }
}
