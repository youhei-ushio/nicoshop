<?php

declare(strict_types=1);

namespace Tests\Unit\Sales\Order;

use App\Contexts\Sales\Domain\Entity\Order;
use App\Contexts\Sales\Domain\Persistence\OrderRecord;
use App\Contexts\Sales\Domain\Persistence\OrderRepository;

final class OrderRepositoryMock implements OrderRepository
{
    private array $records = [];
    private int $id = 0;

    public function save(OrderRecord $record): int
    {
        $this->records[++$this->id] = $record;
        return $this->id;
    }

    public function findById(int $id): Order
    {
        return $this->records[$id];
    }

    public function toArray(): array
    {
        return $this->records;
    }
}
