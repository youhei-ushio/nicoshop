<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\EntityRepository;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Persistence\OrderIterator;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Domain\Value\Product;
use BadMethodCallException;
use DateTimeImmutable;

final class OrderRepositoryImpl extends EntityRepository implements OrderRepository
{
    private array $records = [];

    public function __construct(EventChannel $eventChannel)
    {
        parent::__construct(
            eventChannel: $eventChannel,
            entity: Order::class,
        );
    }

    public function save(Order $order): void
    {
        $record = $this->createRecordFromEntity($order);
        $this->records[$record->id] = $record;
    }

    public function findById(string $id): Order
    {
        return $this->restoreEntity($this->records[$id]);
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
