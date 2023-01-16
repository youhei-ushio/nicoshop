<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Entity\Cart;

final class CartRecord
{
    /**
     * @param Cart\Item[] $items
     */
    public function __construct(
        public readonly int $customerUserId,
        public readonly array $items,
    )
    {

    }
}
