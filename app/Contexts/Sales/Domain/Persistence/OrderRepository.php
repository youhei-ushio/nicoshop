<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;

interface OrderRepository
{
    public function save(OrderRecord $record): int;

    public function findById(int $id): Order;

    public function findUnacceptedOrder(): OrderPaginator;
}
