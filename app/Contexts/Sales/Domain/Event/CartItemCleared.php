<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Event;

final class CartItemCleared
{
    public function __construct(
        public readonly int $customerUserId,
    )
    {

    }
}
