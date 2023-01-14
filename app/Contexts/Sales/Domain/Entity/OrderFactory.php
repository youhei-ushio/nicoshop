<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Value\Product;

final class OrderFactory
{
    public function __construct(
        private readonly EventChannel $eventChannel,
        private readonly IdFactory $idFactory,
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
            $this->idFactory->create(),
            $customerUserId,
            $products,
            $this->eventChannel,
        );
    }
}
