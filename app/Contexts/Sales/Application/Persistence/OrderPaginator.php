<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

use App\Contexts\Sales\Domain\Entity\Order;

interface OrderPaginator
{
    public function current(): Order;

    public function total(): int;

    public function perPage(): int;

    public function currentPage(): int;
}
