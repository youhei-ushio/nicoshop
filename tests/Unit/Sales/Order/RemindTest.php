<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Application\UseCase\Order\Remind\Interactor;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;
use PHPUnit\Framework\TestCase;

final class RemindTest extends TestCase
{
    public function testCanRemind(): void
    {
        // 前準備
        $eventChannel = new Mock\EventChannelImpl();
        $repository = new Mock\NotAcceptedOrderRepositoryImpl($eventChannel);
        $subscriber = new class()
        {
            public function __construct(public int $remindedCount = 0)
            {

            }

            public function __invoke(OrderNotYetAccepted $event): void
            {
                // リマインドされた件数をカウント
                $this->remindedCount++;
            }
        };
        $eventChannel->subscribe(OrderNotYetAccepted::class, $subscriber);

        // 実行
        $interactor = new Interactor(
            $repository,
        );
        $interactor->execute();

        // 検証
        // 10000件のうち、9000件が2日前の注文、さらにその内半分の4500件が未受付
        $this->assertSame(4500, $subscriber->remindedCount);
    }
}
