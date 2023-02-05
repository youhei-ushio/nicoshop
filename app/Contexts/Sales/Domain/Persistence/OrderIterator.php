<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;
use Seasalt\Nicoca\Components\Infrastructure\Persistence\EntityIterator;

final class OrderIterator extends EntityIterator
{
   public function current(): Order
    {
        return parent::current();
    }
}
