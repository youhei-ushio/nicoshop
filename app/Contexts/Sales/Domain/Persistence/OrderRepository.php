<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Domain\Persistence;

interface OrderRepository
{
    public function save(OrderRecord $record): void;

    public function findById(int $id): OrderRecord;
}
