<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\UseCase\Order\Store\Input;
final class OrderItem
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
    )
    {

    }
}
