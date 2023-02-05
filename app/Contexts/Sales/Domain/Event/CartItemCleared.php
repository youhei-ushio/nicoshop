<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Event;

use Seasalt\Nicoca\Components\Domain\Event;

final class CartItemCleared extends Event
{
    public function __construct(
        public readonly int $customerUserId,
    )
    {
        parent::__construct();
    }
}
