<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Application\UseCase\Order\Accept\Input;
use App\Contexts\Sales\Application\UseCase\Order\Accept\Interactor;
use BadMethodCallException;
use PHPUnit\Framework\TestCase;

final class AcceptTest extends TestCase
{
    /**
     * @testdox 注文を受け付けられること
     */
    public function testCanAccept(): void
    {
        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $repository = new Mock\OrderRepositoryImpl($eventChannel);
        $orderId = $repository->addTestRecord();

        // 前検証
        $newOrder = current($repository->toArray());
        $this->assertFalse($newOrder->accepted);

        // 実行
        $interactor = new Interactor(
            $repository,
        );
        $interactor->execute(Input::fromArray([
            'id' => $orderId,
        ]));

        // 検証
        $acceptedOrder = current($repository->toArray());
        $this->assertTrue($acceptedOrder->accepted);
    }

    /**
     * @testdox 受付済みの注文を再度受け付けられないこと
     */
    public function testCannotAcceptAnAcceptedOrder(): void
    {
        // 検証内容設定
        $this->expectException(BadMethodCallException::class);
        $this->expectErrorMessage('The order has already been accepted.');

        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $repository = new Mock\OrderRepositoryImpl($eventChannel);
        $orderId = $repository->addTestRecord(accepted: true);

        // 実行
        $interactor = new Interactor(
            $repository,
        );
        $interactor->execute(Input::fromArray([
            'id' => $orderId,
        ]));
    }
}
