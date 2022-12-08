<?php

declare(strict_types=1);

namespace App\Contexts\Sales\Application\Persistence;

interface ProductQuery
{
    /**
     * @param int[] $ids
     */
    public function filterByIds(array $ids): self;

    public function paginate(int $perPage, int $currentPage): self;

    public function get(): ProductPaginator;
}
