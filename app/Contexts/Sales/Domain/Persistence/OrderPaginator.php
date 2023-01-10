<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;
use Iterator;

interface OrderPaginator extends Iterator
{
    public function current(): Order;
}
