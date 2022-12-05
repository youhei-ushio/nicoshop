<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;
use DateTimeImmutable;

final class OrderRecord
{
    /**
     * @param Order\Item[] $items
     */
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly array $items,
        public readonly int $customerUserId,
        public readonly bool $accepted,
        public readonly bool $finished,
    )
    {

    }
}
