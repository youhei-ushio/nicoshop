<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Application\Persistence\ProductPaginator;
use App\Contexts\Sales\Application\Persistence\ProductQuery;
use App\Contexts\Sales\Application\UseCase\Order\Store\Input;
use App\Contexts\Sales\Application\UseCase\Order\Store\Interactor;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;
use App\Contexts\Sales\Domain\Value\Product;
use ArrayIterator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class StoreTest extends TestCase
{
    public function testCanOrder(): void
    {
        // 前準備
        $orderRepository = $this->createOrderRepository();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
        );

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => [
                [
                    'product_id' => 1234,
                    'quantity' => 100,
                ],
                [
                    'product_id' => 555,
                    'quantity' => 1,
                ],
                [
                    'product_id' => 778899,
                    'quantity' => 99999,
                ],
            ],
            'user_id' => 1,
        ]));

        // 検証
        $storedOrder = $orderRepository->findById(1);
        $this->assertSame(1234, $storedOrder->items[0]->product->id);
        $this->assertSame('ポテチ', $storedOrder->items[0]->product->name);
        $this->assertSame(100, $storedOrder->items[0]->quantity);
        $this->assertSame(555, $storedOrder->items[1]->product->id);
        $this->assertSame('プリン', $storedOrder->items[1]->product->name);
        $this->assertSame(1, $storedOrder->items[1]->quantity);
        $this->assertSame(778899, $storedOrder->items[2]->product->id);
        $this->assertSame('高級アイス', $storedOrder->items[2]->product->name);
        $this->assertSame(99999, $storedOrder->items[2]->quantity);
    }

    public function testCannotOrderWithoutItems(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Cannot order without items');

        // 前準備
        $orderRepository = $this->createOrderRepository();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
        );

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => [],
            'user_id' => 1,
        ]));
    }

    public function testCannotOrderWithLargeItem(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The item quantity must not have more than 1000000.');

        // 前準備
        $orderRepository = $this->createOrderRepository();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
        );

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => [
                [
                    'product_id' => 1234,
                    'quantity' => 3000000,
                ],
            ],
            'user_id' => 1,
        ]));
    }

    /**
     * Infrastructure Mock
     */
    private function createOrderRepository(): OrderRepository
    {
        return new class() implements OrderRepository
        {
            private array $records = [];
            private int $id = 0;

            public function save(OrderRecord $record): void
            {
                $this->records[++$this->id] = $record;
            }

            public function findById(int $id): OrderRecord
            {
                return $this->records[$id];
            }
        };
    }

    /**
     * Infrastructure Mock
     */
    private function createProductQuery(): ProductQuery
    {
        return new class() implements ProductQuery
        {
            public function filterByIds(array $ids): ProductQuery
            {
                return $this;
            }

            public function paginate(int $perPage, int $currentPage): ProductQuery
            {
                return $this;
            }

            public function execute(): ProductPaginator
            {
                $items = [
                    new Product(
                        1234,
                        'ポテチ',
                        130,
                    ),
                    new Product(
                        555,
                        'プリン',
                        100,
                    ),
                    new Product(
                        778899,
                        '高級アイス',
                        350,
                    ),
                ];
                return new class($items) extends ArrayIterator implements ProductPaginator
                {
                    public function __construct(private readonly array $items)
                    {
                        parent::__construct($items);
                    }

                    public function current(): Product
                    {
                        return parent::current();
                    }

                    public function total(): int
                    {
                        return count($this->items);
                    }

                    public function perPage(): int
                    {
                        return 100;
                    }

                    public function currentPage(): int
                    {
                        return 1;
                    }

                    public function getById(int $id): Product
                    {
                        foreach ($this as $product) {
                            if ($product->id === $id) {
                                return $product;
                            }
                        }
                        throw new RuntimeException('not found');
                    }
                };
            }
        };
    }
}
