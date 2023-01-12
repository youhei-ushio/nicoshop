<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order\Mock;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Entity\OrderFactory;
use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Value\Product;

final class OrderFactoryImpl implements OrderFactory
{
    public function __construct(
        private readonly EventChannel $eventChannel,
    )
    {

    }

    /**
     * @param Product[] $products
     */
    public function create(
        int $customerUserId,
        array $products,
    ): Order
    {
        return Order::create(
            uniqid(more_entropy: true),
            $customerUserId,
            $products,
            $this->eventChannel,
        );
    }
}
