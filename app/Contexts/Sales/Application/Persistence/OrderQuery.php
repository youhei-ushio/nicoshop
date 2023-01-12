<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

interface OrderQuery
{
    public function onlyUnfinished(): self;

    public function paginate(int $perPage, int $currentPage): self;

    public function get(): OrderPaginator;
}
