<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Persistence\NotAcceptedOrderRepository;
use App\Contexts\Sales\Domain\Persistence\OrderPaginator;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Value\Product;
use DateTimeImmutable;
use Generator;
use IteratorIterator;
use Traversable;

final class NotAcceptedOrderRepositoryImpl implements NotAcceptedOrderRepository
{
    /** @var OrderRecord[] $orders */
    private readonly array $orders;

    public function __construct(
        private readonly EventChannel $eventChannel,
    )
    {
        // 件数多めにしておく
        $orders = [];
        for ($i = 0; $i < 10000; $i++) {
            $orders[] = new OrderRecord(
                id: $i + 1,
                date: new DateTimeImmutable($i > 9000 ? 'now' : '2 days ago'), // 1000件だけ注文直後にしておく
                items: [
                    new Order\Item(
                        product: new Product(
                            id: 9000 + $i,
                            name: "test$i",
                            unitPrice: 100,
                        ),
                        quantity: 1,
                    ),
                ],
                customerUserId: 1,
                accepted: $i % 2 === 0, // 半分は受付済みにしておく
                finished: false,
            );
        }
        $this->orders = $orders;
    }

    private function paginate(): Generator
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

    public function find(): OrderPaginator
    {
        return new class($this->paginate(), $this->eventChannel) extends IteratorIterator implements OrderPaginator
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
}
