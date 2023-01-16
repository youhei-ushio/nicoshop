<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Event;

use App\Contexts\Sales\Domain\Entity\Cart;

final class CartItemAdded
{
    /**
     * @param Cart\Item[] $items
     */
    public function __construct(
        public readonly int $customerUserId,
        public array $items,
    )
    {

    }
}
