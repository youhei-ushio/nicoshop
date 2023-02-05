<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

use App\Contexts\Sales\Domain\Event\OrderAccepted;
use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Event\OrderFinished;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Value\Product;
use BadMethodCallException;
use DateTimeImmutable;
use InvalidArgumentException;
use Seasalt\Nicoca\Components\Domain\Entity;
use Seasalt\Nicoca\Components\Domain\EventChannel;

/**
 * 注文
 */
final class Order extends Entity
{
    /**
     * @param Order\Item[] $items
     * @see OrderFactory
     */
    private function __construct(
        EventChannel $eventChannel,
        private readonly string $id,
        private readonly DateTimeImmutable $date,
        private readonly int $customerUserId,
        private array $items,
        private bool $accepted,
        private bool $finished,
    )
    {
        parent::__construct($eventChannel);
    }

    /**
     * 明細追加
     */
    public function add(
        Product $product,
    ): void
    {
        if (count($this->items) >= 100) {
            // 注文に100を超える明細は登録不可
            throw new InvalidArgumentException('The order must not have more than 100 items.');
        }
        if ($this->has($product)) {
            // 商品は重複不可
            throw new InvalidArgumentException('The product has already been taken.');
        }
        $this->items[] = new Order\Item($product);
    }

    /**
     * 受付
     */
    public function accept(): void
    {
        if ($this->accepted) {
            // 二重の受付は不可
            throw new BadMethodCallException('The order has already been accepted.');
        }
        $this->accepted = true;

        $this->publish(new OrderAccepted(
            id: $this->id,
            date: $this->date,
            items: $this->items,
            customerUserId: $this->customerUserId,
        ));
    }

    /**
     * 受付リマインド
     */
    public function remind(): void
    {
        if ($this->accepted || $this->finished) {
            return;
        }
        $now = new DateTimeImmutable();
        if ($now > $this->date->modify('tomorrow')) {
            // 注文後に未受付のまま1日経過したらリマインド
            $this->publish(new OrderNotYetAccepted(
                id: $this->id,
                date: $this->date,
                items: $this->items,
                customerUserId: $this->customerUserId,
            ));
        }
    }

    /**
     * 完了
     */
    public function done(): void
    {
        if (!$this->accepted) {
            // 受付前の注文は完了不可
            throw new BadMethodCallException('The order not yet accepted.');
        }
        if ($this->finished) {
            // 二重の完了は不可
            throw new BadMethodCallException('The order has already done.');
        }
        $this->finished = true;

        $this->publish(
            new OrderFinished(
                id: $this->id,
                date: $this->date,
                items: $this->items,
                customerUserId: $this->customerUserId,
            )
        );
    }

    /**
     * @param Product[] $products
     * @see OrderFactory
     */
    protected static function create(
        EventChannel $eventChannel,
        string $id,
        int $customerUserId,
        array $products,
    ): self
    {
        if (empty($products)) {
            // 注文には1つ以上の商品が必要
            throw new InvalidArgumentException('Cannot order without products');
        }
        $order = new self(
            eventChannel: $eventChannel,
            id: $id,
            date: new DateTimeImmutable(), // 当日
            customerUserId: $customerUserId,
            items: [],
            accepted: false, // 未受付
            finished: false, // 未完了
        );
        foreach ($products as $product) {
            $order->add($product);
        }
        $order->publish(
            new OrderCreated(
                id: $order->id,
                date: $order->date,
                items: $order->items,
                customerUserId: $order->customerUserId,
            )
        );
        return $order;
    }

    /**
     * 永続化データの復元
     */
    protected static function restore(EventChannel $eventChannel, OrderRecord $record): self
    {
        return new self(
            eventChannel: $eventChannel,
            id: $record->id,
            date: $record->date,
            customerUserId: $record->customerUserId,
            items: $record->items,
            accepted: $record->accepted,
            finished: $record->finished,
        );
    }

    /**
     * 永続化
     */
    protected function toPersistenceRecord(): OrderRecord
    {
        return new OrderRecord(
            id: $this->id,
            date: $this->date,
            items: $this->items,
            customerUserId: $this->customerUserId,
            accepted: $this->accepted,
            finished: $this->finished,
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
