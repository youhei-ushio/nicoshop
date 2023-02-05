<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

interface OrdersQuery
{
    public function withoutFinished(): self;

    public function paginate(int $perPage, int $currentPage): self;

    public function get(): OrderPaginator;
}
