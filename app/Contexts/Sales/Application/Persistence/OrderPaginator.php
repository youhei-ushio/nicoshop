<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

interface OrderPaginator
{
    public function current(): OrderQueryResult;

    public function total(): int;

    public function perPage(): int;

    public function currentPage(): int;
}
