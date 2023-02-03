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
use Closure;
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
        $record = Closure::bind(function() use ($order) {
            return $order->toPersistenceRecord();
        }, null, Order::class)->__invoke();
        $this->records[$record->id] = $record;
    }

    public function findById(string $id): Order
    {
        $record = $this->records[$id];
        $eventChannel = $this->eventChannel;
        return Closure::bind(function() use ($record, $eventChannel) {
            return Order::restore($record, $eventChannel);
        }, null, Order::class)->__invoke();
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
