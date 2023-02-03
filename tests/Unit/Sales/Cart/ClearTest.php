<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Cart;

use App\Contexts\Sales\Application\UseCase;
use App\Contexts\Sales\Domain\Entity\Cart;
use App\Contexts\Sales\Domain\Event\CartItemAdded;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Mock\EventChannelImpl;

final class ClearTest extends TestCase
{
    /**
     * @testdox カートの商品を空にできること
     */
    public function testCanAddProductToCart(): void
    {
        // 前準備
        $eventChannel = new EventChannelImpl();
        $subscriber = new class()
        {
            public function __construct(public bool $added = false)
            {

            }

            public function __invoke(CartItemAdded $event): void
            {
                $this->added = true;
            }
        };
        $eventChannel->subscribe(CartItemAdded::class, $subscriber);
        $cartRepository = new Mock\CartRepositoryImpl($eventChannel);
        $interactor = new UseCase\Cart\Add\Interactor(
            $cartRepository,
        );

        // 実行
        $interactor->execute(UseCase\Cart\Add\Input::fromInput([
            'product_id' => 1234,
            'quantity' => 100,
            'customer_user_id' => 1,
        ]));

        // イベント検証
        $this->assertTrue($subscriber->added);
        // 登録内容検証
        /** @var Cart\Item $item */
        $item = current($cartRepository->getItems());
        $this->assertSame(1234, $item->product->id);
        $this->assertSame(100, $item->product->quantity);
    }
}
