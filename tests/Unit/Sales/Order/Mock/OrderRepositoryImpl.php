<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Persistence\OrderIterator;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Domain\Value\Product;
use BadMethodCallException;
use DateTimeImmutable;

final class OrderRepositoryImpl implements OrderRepository
{
    private array $records = [];

    public function __construct(
        private readonly EventChannel $eventChannel,
    )
    {

    }

    public function save(Order $order): void
    {
        $record = $order->toPersistenceRecord();
        $this->records[$record->id] = $record;
    }

    public function findById(string $id): Order
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

    public function addTestRecord(bool $accepted = false, bool $finished = false): string
    {
        $record = new OrderRecord(
            id: uniqid(more_entropy: true),
            date: new DateTimeImmutable(),
            items: [
                new Order\Item(
                    product: new Product(
                        id: 12345,
                        quantity: 1,
                    ),
                ),
            ],
            customerUserId: 1,
            accepted: $accepted,
            finished: $finished,
        );
        $this->records[$record->id] = $record;
        return $record->id;
    }

    public function findUnacceptedOrder(): OrderIterator
    {
        throw new BadMethodCallException('Unused');
    }
}
