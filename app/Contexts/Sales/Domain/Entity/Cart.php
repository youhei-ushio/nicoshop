<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

use App\Contexts\Sales\Domain\Event\CartItemAdded;
use App\Contexts\Sales\Domain\Event\CartItemCleared;
use App\Contexts\Sales\Domain\Persistence\CartRecord;
use App\Contexts\Sales\Domain\Value\Product;
use InvalidArgumentException;
use Seasalt\Nicoca\Components\Domain\Entity;
use Seasalt\Nicoca\Components\Domain\EventChannel;

/**
 * カート
 */
final class Cart extends Entity
{
    /**
     * @param Cart\Item[] $items
     */
    private function __construct(
        private readonly EventChannel $eventChannel,
        private readonly int $customerUserId,
        private array $items,
    )
    {
        parent::__construct($this->eventChannel);
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

    public function clear(): void
    {
        $this->items = [];

        $this->eventChannel->publish(
            new CartItemCleared(
                customerUserId: $this->customerUserId,
            )
        );
    }

    public function toOrder(): array
    {
        return array_map(fn(Cart\Item $item) => $item->product, $this->items);
    }

    /**
     * 永続化データの復元
     */
    protected static function restore(EventChannel $eventChannel, CartRecord $record): self
    {
        return new self(
            eventChannel: $eventChannel,
            customerUserId: $record->customerUserId,
            items: $record->items,
        );
    }

    /**
     * 永続化
     */
    protected function toPersistenceRecord(): CartRecord
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
