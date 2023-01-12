<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity\Order;

use App\Contexts\Sales\Domain\Value\Product;
use InvalidArgumentException;

/**
 * 注文明細
 */
final class Item
{
    public function __construct(
        public readonly Product $product,
    )
    {
        if ($this->product->quantity >= 1000000) {
            // 商品の数量に1000000以上は登録不可
            throw new InvalidArgumentException('The item quantity must not have more than 1000000.');
        }
    }
}
