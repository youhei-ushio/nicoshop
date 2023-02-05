<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Event;

use App\Contexts\Sales\Domain\Entity\Cart;
use Seasalt\Nicoca\Components\Domain\Event;

final class CartItemAdded extends Event
{
    /**
     * @param Cart\Item[] $items
     */
    public function __construct(
        public readonly int $customerUserId,
        public array $items,
    )
    {
        parent::__construct();
    }
}
