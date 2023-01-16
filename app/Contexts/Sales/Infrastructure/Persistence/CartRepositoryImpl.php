<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Domain\Entity\Cart;
use App\Contexts\Sales\Domain\Persistence\CartRecord;
use App\Contexts\Sales\Domain\Persistence\CartRepository;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Value\Product;
use App\Models;
use Illuminate\Support\Facades\DB;

final class CartRepositoryImpl implements CartRepository
{
    public function __construct(
        private readonly EventChannel $eventChannel,
    )
    {

    }

    /**
     * @throws
     */
    public function save(Cart $cart): void
    {
        $record = $cart->toPersistenceRecord();
        DB::transaction(function () use ($record) {
            /** @var Models\Cart $cartRow */
            $cartRow = Models\Cart::query()
                ->where('customer_user_id', $record->customerUserId)
                ->firstOrNew();
            $cartRow->fill([
                'customer_user_id' => $record->customerUserId,
            ])->saveOrFail();

            $cartId = $cartRow->id;
            Models\CartItem::query()
                ->where('cart_id', $cartId)
                ->delete();
            foreach ($record->items as $item) {
                (new Models\CartItem())
                    ->fill([
                        'cart_id' => $cartId,
                        'product_id' => $item->product->id,
                        'quantity' => $item->product->quantity,
                    ])->saveOrFail();
            }
        });
    }

    public function findByCustomerId(int $customerUserId): Cart
    {
        /** @var Models\Cart $cartRow */
        $cartRow = Models\Cart::query()
            ->with([
                'items',
            ])
            ->where('customer_user_id', $customerUserId)
            ->first();
        $record = new CartRecord(
            $customerUserId,
            $cartRow?->items?->map(function (Models\CartItem $itemRow) {
                return new Cart\Item(
                    new Product(
                        $itemRow->product_id,
                        $itemRow->quantity,
                    ),
                );
            })?->toArray() ?? [],
        );
        return Cart::restore($record, $this->eventChannel);
    }
}
