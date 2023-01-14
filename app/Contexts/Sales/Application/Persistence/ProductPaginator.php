<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

use Iterator;

interface ProductPaginator extends Iterator
{
    public function current(): ProductQueryResult;

    public function total(): int;

    public function perPage(): int;

    public function currentPage(): int;
}
