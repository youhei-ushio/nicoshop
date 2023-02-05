<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Entity;

use App\Contexts\Sales\Domain\Value\Product;
use Seasalt\Nicoca\Components\Domain\EntityFactory;

final class OrderFactory extends EntityFactory
{
    /**
     * @param Product[] $products
     */
    public function create(
        int $customerUserId,
        array $products,
    ): Order
    {
        return parent::createEntity(Order::class, $customerUserId, $products);
    }
}
