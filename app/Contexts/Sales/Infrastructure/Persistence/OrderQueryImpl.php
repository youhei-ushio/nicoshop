<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Application\Persistence\OrderQuery;
use App\Contexts\Sales\Application\Persistence\OrderPaginator;
use App\Contexts\Sales\Application\Persistence\OrderQueryResult;
use App\Contexts\Sales\Domain\Value\Product;
use App\Models;
use Illuminate\Database\Eloquent\Builder;
use IteratorIterator;
use Traversable;

final class OrderQueryImpl implements OrderQuery
{
    private int $perPage = 1000;
    private int $currentPage = 1;
    private bool $withoutFinished = false;

    public function withoutFinished(): OrderQuery
    {
        $this->withoutFinished = true;
        return $this;
    }

    public function paginate(int $perPage, int $currentPage): OrderQuery
    {
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        return $this;
    }

    public function get(): OrderPaginator
    {
        $paginator = Models\Order::query()
            ->when($this->withoutFinished, function (Builder $builder) {
                $builder->where('finished', false);
            })
            ->paginate(perPage: $this->perPage, page: $this->currentPage);

        return new class(
            $paginator,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
        ) extends IteratorIterator implements OrderPaginator
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

            public function current(): OrderQueryResult
            {
                /** @var Models\Order $row */
                $row = parent::current();
                return new OrderQueryResult(
                    $row->uuid,
                    $row->order_date->toDateTimeImmutable(),
                    $row->items->map(function (Models\OrderItem $itemRow) {
                        return new Order\Item(
                            new Product(
                                $itemRow->product_id,
                                $itemRow->quantity,
                            ),
                        );
                    })->toArray(),
                    $row->customer_user_id,
                    $row->accepted,
                    $row->finished,
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
