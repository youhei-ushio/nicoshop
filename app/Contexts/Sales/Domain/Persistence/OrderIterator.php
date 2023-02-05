<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\EntityIterator;

final class OrderIterator extends EntityIterator
{
   public function current(): Order
    {
        return parent::current();
    }
}
