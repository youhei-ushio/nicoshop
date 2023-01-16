<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

use App\Contexts\Sales\Domain\Event\CartItemAdded;
use App\Contexts\Sales\Domain\Persistence\CartRecord;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Value\Product;
use InvalidArgumentException;

/**
 * カート
 */
final class Cart
{
    /**
     * @param Cart\Item[] $items
     */
    private function __construct(
        private readonly int $customerUserId,
        private array $items,
        private readonly EventChannel $eventChannel,
    )
    {

    }

    /**
     * 商品追加
     */
    public function add(
        Product $product,
    ): void
    {
        if (count($this->items) >= 100) {
            // カートに100を超える商品は登録不可
            throw new InvalidArgumentException('The cart must not have more than 100 items.');
        }
        if ($this->has($product)) {
            // 商品は重複不可
            throw new InvalidArgumentException('The product has already been taken.');
        }
        $this->items[] = new Cart\Item($product);

        $this->eventChannel->publish(
            new CartItemAdded(
                customerUserId: $this->customerUserId,
                items: $this->items,
            )
        );
    }

    /**
     * 永続化データの復元
     */
    public static function restore(CartRecord $record, EventChannel $eventChannel): self
    {
        return new self(
            customerUserId: $record->customerUserId,
            items: $record->items,
            eventChannel: $eventChannel,
        );
    }

    /**
     * 永続化
     */
    public function toPersistenceRecord(): CartRecord
    {
        return new CartRecord(
            customerUserId: $this->customerUserId,
            items: $this->items,
        );
    }

    private function has(Product $product): bool
    {
        foreach ($this->items as $item) {
            if ($item->product->equals($product)) {
                return true;
            }
        }
        return false;
    }
}
