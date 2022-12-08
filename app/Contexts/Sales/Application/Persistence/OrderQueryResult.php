<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;
use DateTimeImmutable;

final class OrderQueryResult
{
    /**
     * @param Order\Item[] $items
     */
    public function __construct(
        public readonly int $id,
        public readonly DateTimeImmutable $date,
        public readonly array $items,
        public readonly int $customerUserId,
        public readonly bool $accepted,
        public readonly bool $finished,
    )
    {

    }
}
