<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Application\UseCase\Order\Store\Input;
use App\Contexts\Sales\Application\UseCase\Order\Store\Interactor;
use App\Contexts\Sales\Domain\Event\OrderCreated;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StoreTest extends TestCase
{
    /**
     * @testdox 注文が作成できること
     */
    public function testCanOrder(): void
    {
        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $subscriber = new class()
        {
            public function __construct(public bool $created = false)
            {

            }

            public function __invoke(OrderCreated $event): void
            {
                $this->created = true;
            }
        };
        $eventChannel->subscribe(OrderCreated::class, $subscriber);
        $orderFactory = new Mock\OrderFactoryImpl($eventChannel);
        $orderRepository = new Mock\OrderRepositoryImpl($eventChannel);
        $interactor = new Interactor(
            $orderFactory,
            $orderRepository,
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
        $this->assertTrue($subscriber->created);
        $storedOrder = current($orderRepository->toArray());
        $this->assertSame(1234, $storedOrder->items[0]->product->id);
        $this->assertSame(100, $storedOrder->items[0]->product->quantity);
        $this->assertSame(555, $storedOrder->items[1]->product->id);
        $this->assertSame(1, $storedOrder->items[1]->product->quantity);
        $this->assertSame(778899, $storedOrder->items[2]->product->id);
        $this->assertSame(99999, $storedOrder->items[2]->product->quantity);
    }

    /**
     * @testdox 明細の無い注文は作成できないこと
     */
    public function testCannotOrderWithoutItems(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Cannot order without products');

        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $orderFactory = new Mock\OrderFactoryImpl($eventChannel);
        $orderRepository = new Mock\OrderRepositoryImpl($eventChannel);
        $interactor = new Interactor(
            $orderFactory,
            $orderRepository,
        );

        // 実行
        $interactor->execute(Input::fromArray([
            'items' => [],
            'user_id' => 1,
        ]));
    }

    /**
     * @testdox 数量が最大値の明細を持つ注文を作成できること
     */
    public function testCanOrderWithMaxQuantityItem(): void
    {
        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $orderFactory = new Mock\OrderFactoryImpl($eventChannel);
        $orderRepository = new Mock\OrderRepositoryImpl($eventChannel);
        $interactor = new Interactor(
            $orderFactory,
            $orderRepository,
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
        $this->assertSame(999999, $storedOrder->items[0]->product->quantity);
    }

    /**
     * @testdox 数量の最大値を超える明細は追加できないこと
     */
    public function testCannotOrderWithLargeItem(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The item quantity must not have more than 1000000.');

        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $orderFactory = new Mock\OrderFactoryImpl($eventChannel);
        $orderRepository = new Mock\OrderRepositoryImpl($eventChannel);
        $interactor = new Interactor(
            $orderFactory,
            $orderRepository,
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

    /**
     * @testdox 明細を最大数まで追加できること
     */
    public function testCanOrderWithMaxItems(): void
    {
        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $orderFactory = new Mock\OrderFactoryImpl($eventChannel);
        $orderRepository = new Mock\OrderRepositoryImpl($eventChannel);
        $interactor = new Interactor(
            $orderFactory,
            $orderRepository,
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

    /**
     * @testdox 最大数を超える明細は追加できないこと
     */
    public function testCannotOrderWithTooManyItems(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The order must not have more than 100 items.');

        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $orderFactory = new Mock\OrderFactoryImpl($eventChannel);
        $orderRepository = new Mock\OrderRepositoryImpl($eventChannel);
        $interactor = new Interactor(
            $orderFactory,
            $orderRepository,
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

    /**
     * @testdox 同じ商品を複数の明細で追加できないこと
     */
    public function testCannotOrderWithSameProducts(): void
    {
        // 検証内容設定
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The product has already been taken.');

        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $orderFactory = new Mock\OrderFactoryImpl($eventChannel);
        $orderRepository = new Mock\OrderRepositoryImpl($eventChannel);
        $interactor = new Interactor(
            $orderFactory,
            $orderRepository,
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
}
