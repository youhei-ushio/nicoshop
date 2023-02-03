<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

use App\Contexts\Sales\Domain\Persistence\EventChannel;
use App\Contexts\Sales\Domain\Value\Product;
use Closure;

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
        $id = $this->idFactory->create();
        $eventChannel = $this->eventChannel;
        return Closure::bind(function() use ($id, $customerUserId, $products, $eventChannel) {
            return Order::create(
                $id,
                $customerUserId,
                $products,
                $eventChannel,
            );
        }, null, Order::class)->__invoke();
    }
}
