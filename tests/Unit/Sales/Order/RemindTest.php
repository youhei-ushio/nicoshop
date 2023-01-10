<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Application\UseCase\Order\Remind\Interactor;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;
use PHPUnit\Framework\TestCase;

final class RemindTest extends TestCase
{
    private int $remindedCount = 0;

    public function testCanRemind(): void
    {
        // 前準備
        $repository = new NotAcceptedOrderRepositoryMock();
        $eventChannel = new EventChannelMock();
        $this->remindedCount = 0;
        $eventChannel->subscribe(OrderNotYetAccepted::class, function () {
            // リマインドされた件数をカウント
            $this->remindedCount++;
        });

        // 実行
        $interactor = new Interactor(
            $repository,
            $eventChannel,
        );
        $interactor->execute();

        // 検証
        // 10000件のうち、9000件が2日前の注文、さらにその内半分の4500件が未受付
        $this->assertSame(4500, $this->remindedCount);
    }
}
