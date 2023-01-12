<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Value;

/**
 * 注文商品
 */
final class Product
{
    public function __construct(
        public readonly int $id,
        public readonly int $quantity,
    )
    {

    }

    public function equals(self $product): bool
    {
        return $this->id === $product->id;
    }
}
