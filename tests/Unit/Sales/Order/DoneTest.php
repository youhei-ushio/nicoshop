<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Application\UseCase\Order\Done\Input;
use App\Contexts\Sales\Application\UseCase\Order\Done\Interactor;
use App\Contexts\Sales\Domain\Event\OrderFinished;
use BadMethodCallException;
use PHPUnit\Framework\TestCase;

final class DoneTest extends TestCase
{
    /**
     * @testdox 注文を完了できること
     */
    public function testCanDone(): void
    {
        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $subscriber = new class()
        {
            public function __construct(public bool $finished = false)
            {

            }

            public function __invoke(OrderFinished $event): void
            {
                $this->finished = true;
            }
        };
        $eventChannel->subscribe(OrderFinished::class, $subscriber);
        $repository = new Mock\OrderRepositoryImpl($eventChannel);
        $orderId = $repository->addTestRecord(accepted: true);

        // 前検証
        $acceptedOrder = current($repository->toArray());
        $this->assertFalse($acceptedOrder->finished);

        // 実行
        $interactor = new Interactor(
            $repository,
        );
        $interactor->execute(Input::fromArray([
            'id' => $orderId,
        ]));

        // 検証
        $finishedOrder = current($repository->toArray());
        $this->assertTrue($finishedOrder->finished);
        $this->assertTrue($subscriber->finished);
    }

    /**
     * @testdox 受け付けられていない注文を完了できないこと
     */
    public function testCannotDoneAnUnacceptedOrder(): void
    {
        // 検証内容設定
        $this->expectException(BadMethodCallException::class);
        $this->expectErrorMessage('The order not yet accepted.');

        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $repository = new Mock\OrderRepositoryImpl($eventChannel);
        $orderId = $repository->addTestRecord();

        // 実行
        $interactor = new Interactor(
            $repository,
        );
        $interactor->execute(Input::fromArray([
            'id' => $orderId,
        ]));
    }

    /**
     * @testdox 完了済みの注文を再度完了できないこと
     */
    public function testCannotDoneAFinishedOrder(): void
    {
        // 検証内容設定
        $this->expectException(BadMethodCallException::class);
        $this->expectErrorMessage('The order has already done.');

        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $repository = new Mock\OrderRepositoryImpl($eventChannel);
        $orderId = $repository->addTestRecord(accepted: true, finished: true);

        // 実行
        $interactor = new Interactor(
            $repository,
        );
        $interactor->execute(Input::fromArray([
            'id' => $orderId,
        ]));
    }
}
