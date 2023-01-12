<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Event;

use DateTimeImmutable;

final class OrderCreated
{
    public function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $date,
        public array $items,
        public readonly int $customerUserId,
    )
    {

    }
}
