<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Application\Persistence\CartItemIterator;
use App\Contexts\Sales\Application\Persistence\CartItemQuery;
use App\Contexts\Sales\Application\Persistence\CartItemQueryResult;
use App\Contexts\Sales\Domain\Value\Product;
use App\Models;
use ArrayIterator;
use Illuminate\Database\Eloquent\Collection;
use IteratorIterator;
use LogicException;
use Traversable;

final class CartItemQueryImpl implements CartItemQuery
{
    public function get(int $customerUserId): CartItemIterator
    {
        /** @var Models\Cart $row */
        $row = Models\Cart::query()
            ->with([
                'items',
            ])
            ->where('customer_user_id', $customerUserId)
            ->first();

        return new class(
            $row?->items ?? new Collection(),
            $row?->items?->count() ?? 0,
        ) extends IteratorIterator implements CartItemIterator
        {
            public function __construct(
                Traversable $iterator,
                private readonly int $count,
            )
            {
                parent::__construct($iterator);
            }

            public function current(): CartItemQueryResult
            {
                /** @var Models\CartItem $row */
                $row = parent::current();
                return new CartItemQueryResult(
                    new Product(
                        id: $row->product_id,
                        quantity: $row->quantity,
                    )
                );
            }

            public function count(): int
            {
                return $this->count;
            }

            public function isEmpty(): bool
            {
                return $this->count === 0;
            }
        };
    }
}
