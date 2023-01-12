<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

use App\Contexts\Sales\Domain\Value\Product;

interface OrderFactory
{
    /**
     * @param Product[] $products
     */
    public function create(
        int $customerUserId,
        array $products,
    ): Order;
}
