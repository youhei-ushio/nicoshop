<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Infrastructure\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\EntityRepository;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Persistence\OrderIterator;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Domain\Value\Product;
use App\Models;
use Illuminate\Support\Facades\DB;
use Traversable;

final class OrderRepositoryImpl extends EntityRepository implements OrderRepository
{
    public function __construct(EventChannel $eventChannel)
    {
        parent::__construct(
            eventChannel: $eventChannel,
            entity: Order::class,
        );
    }

    /**
     * @throws
     */
    public function save(Order $order): void
    {
        $record = $this->createRecordFromEntity($order);
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
        $record = $this->createRecordFromRow($orderRow);
        return parent::restoreEntity($record);
    }

    public function findUnacceptedOrder(): OrderIterator
    {
        return new OrderIterator(
            eventChannel: $this->eventChannel,
            entity: $this->entity,
            records: $this->paginateUnacceptedOrder(),
        );
    }

    private function paginateUnacceptedOrder(): Traversable
    {
        $paginator = Models\Order::query()
            ->where('accepted', false)
            ->where('finished', false)
            ->cursorPaginate(perPage: 1000);
        while (true) {
            foreach ($paginator as $orderRow) {
                yield $this->createRecordFromRow($orderRow);
            }
            if ($paginator->nextCursor() === null) {
                break;
            }
            $paginator = Models\Order::query()->cursorPaginate(cursor: $paginator->nextCursor());
        }
    }

    private function createRecordFromRow(Models\Order $orderRow): OrderRecord
    {
        return new OrderRecord(
            id: $orderRow->uuid,
            date: $orderRow->order_date->toDateTimeImmutable(),
            items: $orderRow->items->map(function (Models\OrderItem $itemRow) {
                return new Order\Item(
                    new Product(
                        id: $itemRow->product_id,
                        quantity: $itemRow->quantity,
                    ),
                );
            })->toArray(),
            customerUserId: $orderRow->customer_user_id,
            accepted: $orderRow->accepted,
            finished: $orderRow->finished,
        );
    }
}
