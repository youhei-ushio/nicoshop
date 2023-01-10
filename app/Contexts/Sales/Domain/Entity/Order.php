<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

use App\Contexts\Sales\Domain\Event\OrderCreated;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Domain\Value\Product;
use BadMethodCallException;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * 注文
 */
final class Order
{
    /**
     * @param Order\Item[] $items
     */
    private function __construct(
        private int|null $id,
        private readonly DateTimeImmutable $date,
        private array $items,
        private readonly int $customerUserId,
        private bool $accepted,
        private bool $finished,
    )
    {

    }

    public static function create(
        int $customerUserId,
    ): self
    {
        return new self(
            id: null, // 永続化まではID無し
            date: new DateTimeImmutable(), // 当日
            items: [],
            customerUserId: $customerUserId,
            accepted: false, // 未受付
            finished: false, // 未完了
        );
    }

    /**
     * 明細追加
     */
    public function add(
        Product $product,
        int $quantity,
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
        $this->items[] = new Order\Item($product, $quantity);
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
    }

    /**
     * 受付リマインド
     */
    public function remind(EventChannel $eventChannel): void
    {
        if ($this->accepted || $this->finished) {
            return;
        }
        $now = new DateTimeImmutable();
        if ($now > $this->date->modify('tomorrow')) {
            // 注文後に未受付のまま1日経過したらリマインド
            $eventChannel->publish(new OrderNotYetAccepted(
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
    }

    /**
     * 永続化
     */
    public function save(OrderRepository $repository, EventChannel $eventChannel): void
    {
        if (empty($this->items)) {
            // 注文には1つ以上の明細が必要
            throw new InvalidArgumentException('Cannot order without items');
        }
        $id = $repository->save(new OrderRecord(
            id: $this->id,
            date: $this->date,
            items: $this->items,
            customerUserId: $this->customerUserId,
            accepted: $this->accepted,
            finished: $this->finished,
        ));
        if ($this->id === null) {
            $eventChannel->publish(
                new OrderCreated(
                    id: $id,
                    date: $this->date,
                    items: $this->items,
                    customerUserId: $this->customerUserId,
                )
            );
        }
        $this->id = $id;
    }

    /**
     * 永続化データの復元
     */
    public static function restore(OrderRecord $record): self
    {
        return new self(
            id: $record->id,
            date: $record->date,
            items: $record->items,
            customerUserId: $record->customerUserId,
            accepted: $record->accepted,
            finished: $record->finished,
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
