<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Persistence\OrderIterator;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Domain\Value\Product;
use App\Models;
use Illuminate\Support\Facades\DB;
use IteratorIterator;
use Traversable;

final class OrderRepositoryImpl implements OrderRepository
{
    public function __construct(
        private readonly EventChannel $eventChannel,
    )
    {

    }

    /**
     * @throws
     */
    public function save(Order $order): void
    {
        $record = $order->toPersistenceRecord();
        DB::transaction(function () use ($record) {
            /** @var Models\Order $orderRow */
            $orderRow = Models\Order::query()
                ->where('uuid', $record->id)
                ->firstOrNew();
            $orderRow->fill([
                'uuid' => $record->id,
                'customer_user_id' => $record->customerUserId,
                'order_date' => $record->date->format('Y-m-d'),
                'accepted' => $record->accepted,
                'finished' => $record->finished,
            ])->saveOrFail();

            $orderId = $orderRow->id;
            Models\OrderItem::query()
                ->where('order_id', $orderId)
                ->delete();
            foreach ($record->items as $item) {
                (new Models\OrderItem())
                    ->fill([
                        'order_id' => $orderId,
                        'product_id' => $item->product->id,
                        'quantity' => $item->product->quantity,
                    ])->saveOrFail();
            }
        });
    }

    public function findById(string $id): Order
    {
        /** @var Models\Order $orderRow */
        $orderRow = Models\Order::query()
            ->with([
                'items',
            ])
            ->where('uuid', $id)
            ->firstOrFail();
        $record = new OrderRecord(
            $orderRow->uuid,
            $orderRow->order_date->toDateTimeImmutable(),
            array_map(function (Models\OrderItem $itemRow) {
                return new Order\Item(
                    new Product(
                        $itemRow->product_id,
                        $itemRow->quantity,
                    ),
                );
            }, $orderRow->items),
            $orderRow->customer_user_id,
            $orderRow->accepted,
            $orderRow->finished,
        );
        return Order::restore($record, $this->eventChannel);
    }

    public function findUnacceptedOrder(): OrderIterator
    {
        return new class($this->paginateUnacceptedOrder(), $this->eventChannel) extends IteratorIterator implements OrderIterator
        {
            public function __construct(Traversable $iterator, private readonly EventChannel $eventChannel)
            {
                parent::__construct($iterator);
            }

            public function current(): Order
            {
                return Order::restore(parent::current(), $this->eventChannel);
            }
        };
    }

    private function paginateUnacceptedOrder(): Traversable
    {
        $paginator = Models\Order::query()
            ->where('accepted', false)
            ->where('finished', false)
            ->cursorPaginate(perPage: 1000);
        while (true) {
            foreach ($paginator as $order) {
                yield $order;
            }
            if ($paginator->nextCursor() === null) {
                break;
            }
            $paginator = Models\Order::query()->cursorPaginate(cursor: $paginator->nextCursor());
        }
    }
}
