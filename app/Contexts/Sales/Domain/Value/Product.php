<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Value;

use InvalidArgumentException;

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
        if ($id <= 0) {
            throw new InvalidArgumentException('The id must be at least 1.');
        }
        if ($quantity <= 0) {
            throw new InvalidArgumentException('The quantity must be at least 1.');
        }
    }

    public function equals(self $product): bool
    {
        return $this->id === $product->id;
    }
}
