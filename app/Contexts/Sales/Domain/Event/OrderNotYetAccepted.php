<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Event;

use App\Contexts\Sales\Domain\Entity\Order;
use DateTimeImmutable;

final class OrderNotYetAccepted
{
    /**
     * @param Order\Item[] $items
     */
    public function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $date,
        public array $items,
        public readonly int $customerUserId,
    )
    {

    }
}
