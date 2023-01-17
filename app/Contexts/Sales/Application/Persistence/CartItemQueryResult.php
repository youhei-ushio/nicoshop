<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

final class CartItemQueryResult
{
    public function __construct(
        public readonly int $productId,
        public readonly string $productName,
        public readonly int $quantity,
    )
    {

    }
}
