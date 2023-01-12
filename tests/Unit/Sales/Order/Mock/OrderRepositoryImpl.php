<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Persistence\OrderPaginator;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Domain\Value\Product;
use BadMethodCallException;
use DateTimeImmutable;

final class OrderRepositoryImpl implements OrderRepository
{
    private array $records = [];
    private int $id = 0;

    public function __construct(
        private readonly EventChannel $eventChannel,
    )
    {

    }

    public function save(OrderRecord $record): int
    {
        if ($record->id === null) {
            $id = ++$this->id;
        } else {
            $id = $record->id;
        }
        $this->records[$id] = new OrderRecord(
            id: $id,
            date: $record->date,
            items: $record->items,
            customerUserId: $record->customerUserId,
            accepted: $record->accepted,
            finished: $record->finished,
        );
        return $this->id;
    }

    public function findById(int $id): Order
    {
        return Order::restore($this->records[$id], $this->eventChannel);
    }

    /**
     * @return OrderRecord[]
     */
    public function toArray(): array
    {
        return $this->records;
    }

    public function addTestRecord(bool $accepted = false, bool $finished = false): int
    {
        return $this->save(new OrderRecord(
            id: null,
            date: new DateTimeImmutable(),
            items: [
                new Order\Item(
                    product: new Product(
                        id: 12345,
                        name: 'TEST!!!!',
                        unitPrice: 100,
                    ),
                    quantity: 1,
                ),
            ],
            customerUserId: 1,
            accepted: $accepted,
            finished: $finished,
        ));
    }

    public function findUnacceptedOrder(): OrderPaginator
    {
        throw new BadMethodCallException('Unused');
    }
}
