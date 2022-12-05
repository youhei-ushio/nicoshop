<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Value;

/**
 * 販売商品
 */
final class Product
{
    /**
     * @param int $unitPrice 販売単価
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $unitPrice,
    )
    {

    }

    public function equals(self $product): bool
    {
        return $this->id === $product->id;
    }
}
