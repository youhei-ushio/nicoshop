<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Application\Persistence\CartItemPaginator;
use App\Contexts\Sales\Application\Persistence\CartItemQuery;
use App\Contexts\Sales\Application\Persistence\CartItemQueryResult;
use App\Models;
use Illuminate\Database\Eloquent\Collection;
use IteratorIterator;
use Traversable;

final class CartItemQueryImpl implements CartItemQuery
{
    private int $perPage = 20;
    private int $currentPage = 1;

    public function paginate(int $currentPage): CartItemQuery
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function get(int $customerUserId): CartItemPaginator
    {
        /** @var Models\Cart $cartRow */
        $cartRow = Models\Cart::query()
            ->where('customer_user_id', $customerUserId)
            ->first();

        if ($cartRow === null) {
            $items = new Collection();
        } else {
            $items = Models\CartItem::query()
                ->with([
                    'product',
                ])
                ->where('cart_id', $cartRow->id)
                ->paginate(perPage: $this->perPage, page: $this->currentPage);
        }

        return new class(
            $items,
            $items->total(),
            $this->perPage,
            $this->currentPage,
        ) extends IteratorIterator implements CartItemPaginator
        {
            public function __construct(
                Traversable $iterator,
                private readonly int $total,
                private readonly int $perPage,
                private readonly int $currentPage,
            )
            {
                parent::__construct($iterator);
            }

            public function current(): CartItemQueryResult
            {
                /** @var Models\CartItem $row */
                $row = parent::current();
                return new CartItemQueryResult(
                    productId: $row->product_id,
                    productName: $row->product->name,
                    quantity: $row->quantity,
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
