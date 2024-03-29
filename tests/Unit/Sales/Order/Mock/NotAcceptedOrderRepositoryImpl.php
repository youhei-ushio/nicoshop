<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Persistence\OrderIterator;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Domain\Value\Product;
use BadMethodCallException;
use DateTimeImmutable;
use Generator;
use Seasalt\Nicoca\Components\Domain\EventChannel;
use Seasalt\Nicoca\Components\Infrastructure\Persistence\EntityRepositoryImpl;

final class NotAcceptedOrderRepositoryImpl extends EntityRepositoryImpl implements OrderRepository
{
    /** @var OrderRecord[] $orders */
    private readonly array $orders;

    /**
     * @throws
     */
    public function __construct(EventChannel $eventChannel)
    {
        parent::__construct(
            eventChannel: $eventChannel,
            entity: Order::class,
        );

        // 件数多めにしておく
        $orders = [];
        for ($i = 0; $i < 10000; $i++) {
            $orders[] = new OrderRecord(
                id: uniqid(prefix: "test$i", more_entropy: true),
                date: new DateTimeImmutable($i > 9000 ? 'now' : '2 days ago'), // 1000件だけ注文直後にしておく
                items: [
                    new Order\Item(
                        product: new Product(
                            id: 9000 + $i,
                            quantity: 1,
                        ),
                    ),
                ],
                customerUserId: 1,
                accepted: $i % 2 === 0, // 半分は受付済みにしておく
                finished: false,
            );
        }
        $this->orders = $orders;
    }

    private function paginateUnacceptedOrder(): Generator
    {
        // ページングを疑似的に再現。実務ではEloquentによるget()をlimit=1000で実行。
        $perPage = 1000;
        $total = count($this->orders);
        $lastPage = (int)ceil($total / $perPage);
        $currentPage = 1;
        do {
            $orders = array_slice($this->orders, $perPage * ($currentPage - 1), $perPage);
            foreach ($orders as $order) {
                yield $order;
            }
            $currentPage++;
        } while ($currentPage <= $lastPage);
    }

    public function save(Order $order): void
    {
        throw new BadMethodCallException('Unused');
    }

    public function findById(string $id): Order
    {
        throw new BadMethodCallException('Unused');
    }

    public function findUnacceptedOrder(): OrderIterator
    {
        return new OrderIterator(
            eventChannel: $this->eventChannel,
            entity: $this->entity,
            records: $this->paginateUnacceptedOrder(),
        );
    }
}
