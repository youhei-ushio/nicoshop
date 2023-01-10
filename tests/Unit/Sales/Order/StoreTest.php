<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Application\Persistence\ProductQuery;
use App\Contexts\Sales\Application\UseCase\Order\Store\Input;
use App\Contexts\Sales\Application\UseCase\Order\Store\Interactor;
use App\Contexts\Sales\Domain\Value\Product;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StoreTest extends TestCase
{
    public function testCanOrder(): void
    {
        // 前準備
        $orderRepository = new OrderRepositoryMock();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
            new EventChannelMock(),
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

        $storedOrder = current($orderRepository->toArray());
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
        $orderRepository = new OrderRepositoryMock();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
            new EventChannelMock(),
        );

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => [],
            'user_id' => 1,
        ]));
    }

    public function testCanOrderWithMaxQuantityItem(): void
    {
        // 前準備
        $orderRepository = new OrderRepositoryMock();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
            new EventChannelMock(),
        );

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => [
                [
                    'product_id' => 1234,
                    'quantity' => 999999,
                ],
            ],
            'user_id' => 1,
        ]));

        // 検証
        $storedOrder = current($orderRepository->toArray());
        $this->assertSame(999999, $storedOrder->items[0]->quantity);
    }

    public function testCannotOrderWithLargeItem(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The item quantity must not have more than 1000000.');

        // 前準備
        $orderRepository = new OrderRepositoryMock();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
            new EventChannelMock(),
        );

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => [
                [
                    'product_id' => 1234,
                    'quantity' => 1000000,
                ],
            ],
            'user_id' => 1,
        ]));
    }

    public function testCanOrderWithMaxItems(): void
    {
        // 前準備
        $orderRepository = new OrderRepositoryMock();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
            new EventChannelMock(),
        );
        $items = [];
        for ($i = 0; $i < 100; ++$i) {
            $items[] = [
                'product_id' => $i + 1,
                'quantity' => 1,
            ];
        }

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => $items,
            'user_id' => 1,
        ]));

        // 検証
        $storedOrder = current($orderRepository->toArray());
        $this->assertSame(100, count($storedOrder->items));
    }

    public function testCannotOrderWithTooManyItems(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The order must not have more than 100 items.');

        // 前準備
        $orderRepository = new OrderRepositoryMock();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
            new EventChannelMock(),
        );
        $items = [];
        for ($i = 0; $i < 100; ++$i) {
            $items[] = [
                'product_id' => $i + 1,
                'quantity' => 1,
            ];
        }
        $items[] = [
            'product_id' => 1234,
            'quantity' => 10,
        ];

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => $items,
            'user_id' => 1,
        ]));
    }

    public function testCannotOrderWithSameProducts(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The product has already been taken.');

        // 前準備
        $orderRepository = new OrderRepositoryMock();
        $interactor = new Interactor(
            $orderRepository,
            $this->createProductQuery(),
            new EventChannelMock(),
        );

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => [
                [
                    'product_id' => 1234,
                    'quantity' => 100,
                ],
                [
                    'product_id' => 1234,
                    'quantity' => 50,
                ],
            ],
            'user_id' => 1,
        ]));
    }

    /**
     * Infrastructure Mock
     */
    private function createProductQuery(): ProductQuery
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

        // 大量の明細向け
        for ($i = 0; $i < 100; ++$i) {
            $items[] = new Product(
                $i + 1,
                "ガムNo.$i",
                $i * 10,
            );
        }
        return new ProductQueryMock($items);
    }
}
