<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

use App\Contexts\Sales\Domain\Value\Product;
use Iterator;

interface ProductPaginator extends Iterator
{
    public function current(): Product;

    public function total(): int;

    public function perPage(): int;

    public function currentPage(): int;

    public function getById(int $id): Product;
}
