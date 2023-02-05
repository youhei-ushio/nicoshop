<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Application\Persistence\OrderQuery;
use App\Contexts\Sales\Application\Persistence\OrderQueryResult;
use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Value\Product;
use App\Models;

final class OrderQueryImpl implements OrderQuery
{
    public function get(string $id): OrderQueryResult
    {
        /** @var Models\Order $row */
        $row = Models\Order::query()
            ->where('uuid', $id)
            ->firstOrFail();

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
}
