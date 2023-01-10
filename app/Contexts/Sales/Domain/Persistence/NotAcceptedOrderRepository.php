<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

interface NotAcceptedOrderRepository
{
    public function find(): OrderPaginator;
}
