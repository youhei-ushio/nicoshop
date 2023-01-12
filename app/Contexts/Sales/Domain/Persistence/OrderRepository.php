<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;

interface OrderRepository
{
    public function save(Order $order): void;

    public function findById(string $id): Order;

    public function findUnacceptedOrder(): OrderPaginator;
}
